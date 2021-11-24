import PageActions from "./PageActions";
import { Button, Space } from "antd";
import { ButtonType } from "antd/lib/button/button";
import { CheckOutlined } from "@ant-design/icons";
import React, { FC, useEffect, useMemo, useState } from "react";
import { useNavigate, useLocation } from "react-router-dom";

interface UpdatePageActionsProps {
  save(): void;
  loading?: boolean;
  error?: boolean;
  success?: boolean;
  exitRoute: string;
  createRoute: string;
  touched: boolean;
  afterSaveRedirect?: string;
  afterSaveViewRedirect?: string;
}

interface ButtonConfig {
  key: string;
  type?: ButtonType;
  label: string;
  redirect?: string;
  replace?: boolean;
  useLocation?: boolean;
}

const UpdatePageActions: FC<UpdatePageActionsProps> = ({
  save,
  loading,
  error,
  success,
  exitRoute,
  createRoute,
  touched,
  afterSaveRedirect,
  afterSaveViewRedirect,
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
        redirect: afterSaveRedirect,
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

    if (afterSaveViewRedirect !== undefined) {
      buttonList.push({
        key: "save-view",
        label: "Сохранить и посмотреть",
        redirect: afterSaveViewRedirect,
        useLocation: true,
      });
    }

    return buttonList;
  }, [afterSaveRedirect, exitRoute, createRoute, afterSaveViewRedirect]);

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
      if (button.useLocation) {
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
                !touched &&
                success &&
                lastClickKey === button.key && <CheckOutlined />
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
