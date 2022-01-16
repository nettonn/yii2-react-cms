import React, { FC } from "react";
import { Breadcrumb } from "antd";
import { HomeOutlined } from "@ant-design/icons";
import { Link } from "react-router-dom";
import { routeNames } from "../../routes";

export interface BreadcrumbItem {
  path?: string;
  label: string;
}

export interface BreadcrumbsProps {
  items: BreadcrumbItem[];
}

const Breadcrumbs: FC<BreadcrumbsProps> = ({ items }) => {
  return (
    <Breadcrumb>
      <Breadcrumb.Item>
        <Link to={routeNames.home}>
          <HomeOutlined />
        </Link>
      </Breadcrumb.Item>
      {items.map((item, index) => (
        <Breadcrumb.Item key={index}>
          {item.path ? (
            <Link to={item.path}>
              <span>{item.label}</span>
            </Link>
          ) : (
            <span>{item.label}</span>
          )}
        </Breadcrumb.Item>
      ))}
    </Breadcrumb>
  );
};

export default Breadcrumbs;
