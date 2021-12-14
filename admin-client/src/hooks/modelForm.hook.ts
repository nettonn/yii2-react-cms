import RestService from "../api/RestService";
import { prepareAntdValidationErrors } from "../utils/functions";
import { Form, message } from "antd";
import { FieldData } from "rc-field-form/es/interface";
import { useEffect, useState } from "react";
import { ValidateErrorEntity } from "rc-field-form/lib/interface";
import { IModel, IModelOptions } from "../types";
import { useQuery, useMutation } from "react-query";
import { queryClient } from "../http/query-client";
import { useNavigate } from "react-router-dom";
import { RouteNames } from "../routes";
import { useIsMounted } from "usehooks-ts";
const pretty = require("pretty");

export function useModelForm<
  T extends IModel = IModel,
  M extends IModelOptions = IModelOptions
>(
  id: number | string | undefined,
  modelService: RestService,
  makePrettyFields: (keyof T)[] = []
) {
  const isMounted = useIsMounted();
  const [isInit, setIsInit] = useState(false);
  const [form] = Form.useForm();
  const [isTouchedAfterSubmit, setIsTouchedAfterSubmit] = useState(false);
  const [validationErrors, setValidationErrors] = useState<FieldData[] | null>(
    null
  );
  const [newId, setNewId] = useState<number | string | null>(null);
  const [viewUrl, setViewUrl] = useState<string | undefined>();
  const [versionsUrl, setVersionsUrl] = useState<string | undefined>();

  const navigate = useNavigate();

  const isCreateForm = !id;
  const isUpdateForm = !!id;

  const {
    data: modelDefaults,
    isFetched: modelDefaultsIsFetched,
    isFetching: modelDefaultsIsFetching,
    isSuccess: modelDefaultsIsSuccess,
    error: modelDefaultsError,
  } = useQuery(
    modelService.modelDefaultsQueryKey(),
    async ({ signal }) => {
      const result = await modelService.modelDefaults<T>(signal);

      if (result.success) {
        return result.data;
      }
      message.error(result.error);
      throw new Error(result.error);
    },
    {
      enabled: isCreateForm,
      // cacheTime: 0,
    }
  );

  const {
    data: modelOptions,
    isFetching: modelOptionsIsFetching,
    isFetched: modelOptionsIsFetched,
    isSuccess: modelOptionsIsSuccess,
    error: modelOptionsError,
  } = useQuery(
    modelService.modelOptionsQueryKey(),
    async ({ signal }) => {
      const result = await modelService.modelOptions<M>(signal);
      if (result.success) {
        return result.data;
      }
      message.error(result.error);
      throw new Error(result.error);
    },
    {
      refetchOnMount: false,
    }
  );

  const {
    data: viewData,
    isFetched: viewIsFetched,
    isFetching: viewIsFetching,
    isSuccess: viewIsSuccess,
    error: viewError,
  } = useQuery(
    [modelService.viewQueryKey(), id],
    async ({ signal }) => {
      if (!id) throw Error("Id not set");
      const result = await modelService.view<T>(id, signal);

      if (result.success) {
        if (result.data?.view_url) {
          setViewUrl(result.data.view_url);
        }
        if (result.data?.versions_url) {
          setVersionsUrl(result.data.versions_url);
        }
        return result.data;
      }
      if (result.status === 404) {
        navigate(RouteNames.error.e404, { replace: true });
      } else {
        message.error(result.error);
      }
      throw new Error(result.error);
    },
    {
      enabled: isUpdateForm,
      // cacheTime: 0,
    }
  );

  const {
    isLoading: submitIsLoading,
    isSuccess: submitIsSuccess,
    mutate: onSubmit,
    error: submitError,
  } = useMutation(async (values: T) => {
    setValidationErrors(null);
    setIsTouchedAfterSubmit(false);

    if (makePrettyFields && makePrettyFields.length) {
      for (let field of makePrettyFields) {
        if (values[field]) {
          values[field] = pretty(values[field]);
        }
      }
    }

    const result = await (isUpdateForm
      ? modelService.update<T>(id, values)
      : modelService.create<T>(values));

    if (result.success) {
      if (result.data?.view_url) setViewUrl(result.data.view_url);
      if (result.data?.versions_url) setVersionsUrl(result.data.versions_url);

      if (isCreateForm) {
        if (result.data && isMounted()) {
          setNewId(result.data.id);
        }
        form.resetFields();
      } else {
        form.setFieldsValue(result.data);
        queryClient.setQueryData(
          [modelService.viewQueryKey(), id],
          result.data
        );
      }

      return result.data;
    }
    if (result.validationErrors) {
      const antdValidationErrors = prepareAntdValidationErrors(
        result.validationErrors
      );
      setValidationErrors(antdValidationErrors);
      form.setFields(antdValidationErrors);
      // throw new Error(result.error);
    } else {
      message.error(result.error);
      throw new Error(result.error);
    }
  });

  const onValuesChange = (changedFields: any, allFields: any) => {
    // find undefined keys and set it to null
    Object.keys(changedFields).forEach((key) => {
      if (changedFields[key] === undefined) {
        form.setFields([{ name: key, value: null, errors: [] }]);
      } else {
        form.setFields([{ name: key, errors: [] }]);
      }
    });

    setIsTouchedAfterSubmit(true);
  };

  const onFinishFailed = ({ errorFields }: ValidateErrorEntity) => {
    setIsTouchedAfterSubmit(false);
    setValidationErrors(errorFields);
  };

  useEffect(() => {
    if (isInit) return;
    if (
      isUpdateForm &&
      viewIsSuccess &&
      !viewIsFetching &&
      modelOptionsIsSuccess &&
      !modelOptionsIsFetching
    ) {
      setIsInit(true);
    } else if (
      isCreateForm &&
      modelDefaultsIsSuccess &&
      !modelDefaultsIsFetching &&
      modelOptionsIsSuccess &&
      !modelOptionsIsFetching
    ) {
      setIsInit(true);
    }
  }, [
    isInit,
    isCreateForm,
    isUpdateForm,
    viewIsSuccess,
    viewIsFetching,
    modelOptionsIsSuccess,
    modelOptionsIsFetching,
    modelDefaultsIsSuccess,
    modelDefaultsIsFetching,
  ]);

  const error =
    modelDefaultsError || viewError || modelOptionsError || submitError;

  const isDataLoading =
    (!modelDefaultsIsFetched && !viewIsFetched) || !modelOptionsIsFetched;

  const isNotFound = isInit && id && !viewData;

  const isSaveLoading = submitIsLoading || modelOptionsIsFetching;

  const isSaveSuccess =
    submitIsSuccess && modelOptionsIsSuccess && !modelOptionsIsFetching;

  const initData = (isCreateForm ? modelDefaults : viewData) as T;

  return {
    id,
    newId,
    viewUrl,
    versionsUrl,
    isCreateForm,
    isUpdateForm,
    form,
    initData,
    modelOptions,
    modelDefaults,
    error,
    validationErrors,
    isInit,
    isDataLoading,
    isSaveLoading,
    isSaveSuccess,
    isTouchedAfterSubmit,
    onSubmit,
    isNotFound,
    onFinishFailed,
    onValuesChange,
  };
}
