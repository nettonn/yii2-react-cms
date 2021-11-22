import React, { FC } from "react";
import Breadcrumbs, { BreadcrumbItem } from "../Breadcrumbs";
import { PageHeader as PageHeaderAntd } from "antd";
import "./PageHeader.css";
import { useNavigate } from "react-router-dom";

interface PageHeaderProps {
  title: string;
  backPath?: string;
  breadcrumbItems?: BreadcrumbItem[];
  extra?: React.ReactNode;
}

const PageHeader: FC<PageHeaderProps> = ({
  title,
  backPath,
  breadcrumbItems = [],
  extra,
}) => {
  const navigate = useNavigate();

  return (
    <PageHeaderAntd
      className="app-page-header"
      title={title}
      onBack={backPath ? () => navigate(backPath) : undefined}
      extra={extra}
      breadcrumbRender={() => <Breadcrumbs items={breadcrumbItems} />}
    />
  );
};

export default PageHeader;
