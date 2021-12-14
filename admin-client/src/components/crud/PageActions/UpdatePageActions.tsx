import PageActions from "./PageActions";
import { Button, Space } from "antd";
import { ButtonType } from "antd/lib/button/button";
import { CheckOutlined } from "@ant-design/icons";
import React, { FC, useEffect, useMemo, useState } from "react";
import { useNavigate, useLocation } from "react-router-dom";
import { withoutBaseUrl } from "../../../utils/functions";

interface UpdatePageActionsProps {
  save(): void;
  loading?: boolean;
  error?: boolean;
  success?: boolean;
  exitRoute: string;
  createRoute: string;
  touched: boolean;
  updateRoute?: string;
  hasViewUrl?: boolean;
  viewUrl?: string;
  versionsUrl?: string;
}

interface ButtonConfig {
  key: string;
  type?: ButtonType;
  label: string;
  redirect?: string;
  replace?: boolean;
  external?: boolean;
}

const UpdatePageActions: FC<UpdatePageActionsProps> = ({
  save,
  loading,
  error,
  success,
  exitRoute,
  createRoute,
  touched,
  updateRoute,
  hasViewUrl,
  viewUrl,
  versionsUrl,
}) => {
  const [lastClickKey, setLastClickKey] = useState<string | null>(null);
  const [isRedirectNeed, setIsRedirectNeed] = useState(false);
  const { pathname } = useLocation();
  const navigate = useNavigate();

  const buttons = useMemo(() => {
    const buttonList: ButtonConfig[] = [
      {
        key: "save",
        type: "primary",
        label: "Сохранить",
        redirect: updateRoute,
        replace: true,
      },
      {
        key: "save-exit",
        label: "Сохранить и выйти",
        redirect: exitRoute,
      },
      {
        key: "save-add",
        label: "Сохранить и добавить",
        redirect: createRoute,
      },
    ];

    if (hasViewUrl) {
      buttonList.push({
        key: "save-view",
        label: "Сохранить и посмотреть",
        redirect: viewUrl,
        external: true,
      });
    }
    if (versionsUrl) {
      buttonList.push({
        key: "versions",
        label: "Версии",
        redirect: withoutBaseUrl(versionsUrl),
      });
    }

    return buttonList;
  }, [updateRoute, exitRoute, createRoute, hasViewUrl, viewUrl, versionsUrl]);

  const buttonClickHandler = (button: ButtonConfig) => {
    if (loading) return;
    setLastClickKey(button.key);
    setTimeout(() => {
      // if click save and then click other button, success status is saved and redirect starts
      setIsRedirectNeed(true);
    }, 100);

    save();
  };

  useEffect(() => {
    if (!success || !isRedirectNeed || !lastClickKey) return;
    const button = buttons.find((o) => o.key === lastClickKey);
    if (
      isRedirectNeed &&
      button &&
      button.redirect &&
      pathname !== button.redirect
    ) {
      setIsRedirectNeed(false);
      if (button.external) {
        window.location.href = button.redirect;
      } else {
        navigate(button.redirect, { replace: button.replace });
      }
    } else {
      setIsRedirectNeed(false);
    }
  }, [success, navigate, pathname, buttons, isRedirectNeed, lastClickKey]);

  return (
    <PageActions
      content={
        <Space>
          {buttons.map((button) => (
            <Button
              key={button.key}
              type={button.type as any}
              danger={!touched && error && lastClickKey === button.key}
              icon={
                !touched && success && !error && lastClickKey === button.key ? (
                  <CheckOutlined />
                ) : undefined
              }
              onClick={() => buttonClickHandler(button)}
              // loading={loading && lastClickKey === button.key}
              disabled={loading && lastClickKey !== button.key}
            >
              {button.label}
            </Button>
          ))}
        </Space>
      }
    />
  );
};

export default UpdatePageActions;
