import RestService from "../api/RestService";
import {
  prepareAntdValidationErrors,
  requestErrorHandler,
} from "../utils/functions";
import { Form } from "antd";
import { FieldData } from "rc-field-form/es/interface";
import { useEffect, useState } from "react";
import { ValidateErrorEntity } from "rc-field-form/lib/interface";
import { Model, ModelOptions } from "../types";
import { useQuery, useMutation } from "react-query";
import { queryClient } from "../http/query-client";
import { useNavigate } from "react-router-dom";
import { routeNames } from "../routes";
import { useIsMounted } from "usehooks-ts";
const pretty = require("pretty");

export function useModelForm<
  T extends Model = Model,
  M extends ModelOptions = ModelOptions
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
  const [modelClass, setModelClass] = useState<string | undefined>();
  const [modelHasVersions, setModelHasVersions] = useState<boolean>(false);

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
      return await modelService.modelDefaults<T>(signal);
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
      return await modelService.modelOptions<M>(signal);
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
      const data = await modelService.view<T>(id, signal);

      if (data?.view_url) setViewUrl(data.view_url);
      if (data?.model_class) setModelClass(data.model_class);
      if (data?.has_versions) setModelHasVersions(data.has_versions);

      return data;
    },
    {
      enabled: isUpdateForm,
      // cacheTime: 0,
      onError: (e) => {
        const errors = requestErrorHandler(e);
        if (errors.status === 404) {
          navigate(routeNames.error.e404, { replace: true });
        }
      },
    }
  );

  const {
    isLoading: submitIsLoading,
    isSuccess: submitIsSuccess,
    mutate: onSubmit,
    error: submitError,
  } = useMutation(
    async (values: any) => {
      setValidationErrors(null);
      setIsTouchedAfterSubmit(false);

      if (makePrettyFields && makePrettyFields.length) {
        for (let field of makePrettyFields) {
          if (values[field]) {
            values[field] = pretty(values[field]);
          }
        }
      }

      const data = await (isUpdateForm
        ? modelService.update<T>(id, values)
        : modelService.create<T>(values));

      if (data?.view_url) setViewUrl(data.view_url);
      if (data?.model_class) setModelClass(data.model_class);
      if (data?.has_versions) setModelHasVersions(data.has_versions);

      if (isCreateForm) {
        if (data && isMounted()) {
          setNewId(data.id);
        }
        form.resetFields();
      } else {
        form.setFieldsValue(data);
        queryClient.setQueryData([modelService.viewQueryKey(), id], data);
      }

      return data;
    },
    {
      onError: (e) => {
        const errors = requestErrorHandler(e);
        if (errors.validationErrors) {
          const antdValidationErrors = prepareAntdValidationErrors(
            errors.validationErrors
          );
          setValidationErrors(antdValidationErrors);
          form.setFields(antdValidationErrors);
        }
      },
    }
  );

  const onValuesChange = (changedFields: any, allFields: any) => {
    // find undefined values and set it to null, for send null values to server
    Object.entries(changedFields).forEach(([name, value]) => {
      if (value === undefined) {
        form.setFields([{ name, value: null, errors: [] }]);
      } else {
        form.setFields([{ name, errors: [] }]);
      }
    });

    setIsTouchedAfterSubmit(true);
  };

  const onFinishFailed = ({ errorFields }: ValidateErrorEntity) => {
    setIsTouchedAfterSubmit(false);
    setValidationErrors(errorFields);
  };

  const isUpdateFormLoaded =
    isUpdateForm &&
    viewIsSuccess &&
    !viewIsFetching &&
    modelOptionsIsSuccess &&
    !modelOptionsIsFetching;

  const isCreateFormLoaded =
    isCreateForm &&
    modelDefaultsIsSuccess &&
    !modelDefaultsIsFetching &&
    modelOptionsIsSuccess &&
    !modelOptionsIsFetching;

  useEffect(() => {
    if (isInit) return;
    if (isUpdateFormLoaded) {
      setIsInit(true);
    } else if (isCreateFormLoaded) {
      setIsInit(true);
    }
  }, [isInit, isUpdateFormLoaded, isCreateFormLoaded]);

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
    modelClass,
    modelHasVersions,
    viewUrl,
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
