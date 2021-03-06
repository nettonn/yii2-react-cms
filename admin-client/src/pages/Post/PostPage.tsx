import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC, useMemo } from "react";
import { useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import { Col, Form, Input, Row, Switch, Tabs } from "antd";
import rules from "../../utils/rules";
import { routeNames } from "../../routes";
import { Post, PostModelOptions } from "../../models/Post";
import FileUpload from "../../components/crud/form/FileUpload/FileUpload";
import PostService from "../../api/PostService";
import useGenerateAlias from "../../hooks/generateAlias.hook";
import { DEFAULT_ROW_GUTTER } from "../../utils/constants";
import CkeditorInput from "../../components/crud/form/CkeditorInput/CkeditorInput";
import { useQuery } from "react-query";
import { postSectionService } from "../../api/PostSectionService";
import { PostSection } from "../../models/PostSection";
import useModelType from "../../hooks/modelType.hook";
import TagInput from "../../components/crud/form/TagInput/TagInput";

const modelRoutes = routeNames.post;
const postSectionRoutes = routeNames.postSection;

const PostPage: FC = () => {
  const { id, sectionId } = useParams();

  const { data: sectionData } = useQuery(
    [postSectionService.viewQueryKey(), sectionId],
    async ({ signal }) => {
      if (!sectionId) throw Error("Id not set");
      return await postSectionService.view<PostSection>(sectionId, signal);
    },
    {
      refetchOnMount: false,
    }
  );

  const postService = useMemo(() => new PostService(sectionId), [sectionId]);

  const modelForm = useModelForm<Post, PostModelOptions>(id, postService, [
    "content",
  ]);

  const { type } = useModelType(sectionData?.type);

  const getTypeForm = () => {
    if (type === null) return null;

    return null;
  };

  const [onNameFieldChange, onAliasFieldChange] = useGenerateAlias(
    modelForm.form,
    "name",
    "alias"
  );

  const formContent = (initData: Post, modelOptions: PostModelOptions) => (
    <Tabs type="card">
      <Tabs.TabPane tab="??????????" key="common">
        <Row gutter={DEFAULT_ROW_GUTTER}>
          <Col span={24} md={12}>
            <Form.Item label="????????????????" name="name" rules={[rules.required()]}>
              <Input onChange={(e) => onNameFieldChange(e.target.value)} />
            </Form.Item>
          </Col>
          <Col span={24} md={12}>
            <Form.Item
              label="??????????????????"
              name="alias"
              rules={[rules.required()]}
            >
              <Input onChange={(e) => onAliasFieldChange(e.target.value)} />
            </Form.Item>
          </Col>
        </Row>

        <Form.Item label="?????????????? ????????????????" name="description">
          <Input.TextArea autoSize={{ minRows: 3, maxRows: 10 }} />
        </Form.Item>

        <Form.Item label="????????????????????" name="content">
          <CkeditorInput />
        </Form.Item>

        {getTypeForm()}

        <Form.Item label="????????" name="user_tags">
          <TagInput options={modelOptions.tag} />
        </Form.Item>

        <Form.Item label="????????????" name="status" valuePropName="checked">
          <Switch />
        </Form.Item>
      </Tabs.TabPane>
      <Tabs.TabPane tab="??????????" key="files">
        <Form.Item name="images_id" noStyle={true}>
          <FileUpload
            label="??????????????????????"
            accept=".jpg,.png,.gif"
            // multiple={true}
          />
        </Form.Item>
      </Tabs.TabPane>
      <Tabs.TabPane tab="SEO" key="seo">
        <Form.Item label="SEO Title" name="seo_title">
          <Input />
        </Form.Item>
        <Form.Item label="SEO H1" name="seo_h1">
          <Input />
        </Form.Item>
        <Form.Item label="SEO Description" name="seo_description">
          <Input.TextArea />
        </Form.Item>
        <Form.Item label="SEO Keywords" name="seo_keywords">
          <Input.TextArea />
        </Form.Item>
      </Tabs.TabPane>
    </Tabs>
  );

  return (
    <>
      <PageHeader
        title={`${id ? "????????????????????????????" : "????????????????"} ??????????????`}
        backPath={modelRoutes.indexUrl(sectionId)}
        breadcrumbItems={[
          { path: postSectionRoutes.index, label: "?????????????? ??????????????" },
          {
            path: postSectionRoutes.updateUrl(sectionId),
            label: sectionData ? sectionData.name : sectionId ?? "",
          },
          {
            path: modelRoutes.indexUrl(sectionId),
            label: "????????????",
          },
          {
            path: modelRoutes.updateUrl(sectionId, id),
            label: modelForm.initData?.name ?? id,
          },
        ]}
      />

      <ModelForm
        {...modelForm}
        formContent={formContent}
        exitRoute={modelRoutes.indexUrl(sectionId)}
        createRoute={modelRoutes.createUrl(sectionId)}
        updateRoute={modelRoutes.updateUrl(sectionId)}
        hasViewUrl={true}
      />
    </>
  );
};

export default PostPage;
