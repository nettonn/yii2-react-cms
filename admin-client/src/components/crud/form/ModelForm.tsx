import React, { FC } from "react";
import { Form, FormInstance, Spin } from "antd";
import _isEmpty from "lodash/isEmpty";
import { useModelForm } from "../../../hooks/modelForm.hook";
import UpdatePageActions from "./../../crud/PageActions/UpdatePageActions";
import { IModel, IModelOptions } from "../../../types";
import { Navigate } from "react-router-dom";
import { RouteNames } from "../../../routes";
import { stringReplace } from "../../../utils/functions";

interface ModelFormProps {
  modelForm: any | ReturnType<typeof useModelForm>; // how without any?
  formContent(
    initData?: IModel,
    modelOptions?: IModelOptions,
    form?: FormInstance
  ): React.ReactNode;
  exitRoute: string;
  createRoute: string;
  updateRoute: string; // with :id placeholder
  hasViewUrl?: boolean;
}

const ModelForm: FC<ModelFormProps> = ({
  modelForm,
  formContent,
  exitRoute,
  createRoute,
  updateRoute,
  hasViewUrl,
}) => {
  const {
    newId,
    viewUrl,
    form,
    initData,
    isDataLoading,
    isSaveLoading,
    isSaveSuccess,
    isTouchedAfterSubmit,
    isInit,
    error,
    validationErrors,
    onSubmit,
    onFinishFailed,
    onValuesChange,
    modelOptions,
    isNotFound,
    versionsUrl,
  } = modelForm;

  if (!isInit) return <Spin spinning={true} />;

  if (isNotFound) {
    return <Navigate to={RouteNames.error.e404} />;
  }

  const renderForm = () => (
    <Spin spinning={isDataLoading || isSaveLoading}>
      <Form
        form={form}
        name="basic"
        initialValues={initData}
        layout="vertical"
        onFinish={onSubmit}
        onFinishFailed={onFinishFailed}
        onValuesChange={onValuesChange}
        // autoComplete="off"
      >
        {formContent(initData, modelOptions, form)}
      </Form>
    </Spin>
  );

  return (
    <>
      {renderForm()}
      <UpdatePageActions
        save={() => {
          form.submit();
        }}
        loading={isSaveLoading}
        touched={isTouchedAfterSubmit}
        error={!!error || !_isEmpty(validationErrors)}
        success={isSaveSuccess}
        exitRoute={exitRoute}
        createRoute={createRoute}
        updateRoute={
          newId ? stringReplace(updateRoute, { ":id": newId }) : undefined
        }
        hasViewUrl={hasViewUrl}
        viewUrl={viewUrl}
        versionsUrl={versionsUrl}
      />
    </>
  );
};

export default ModelForm;
