import React, { FC, useState, useEffect, ReactElement } from "react";
import { Layout, Menu } from "antd";
import "./Sidebar.css";
import { Link, useLocation } from "react-router-dom";
import { RouteNames } from "../../../routes";
import RouteIcon from "../../ui/RouteIcon";
import { useAppActions } from "../../../hooks/redux";
import { authActions } from "../../../store/reducers/auth";
import { MenuClickEventHandler } from "rc-menu/lib/interface";
import { LogoutOutlined } from "@ant-design/icons";

interface IItem {
  title: string;
  route: string;
  icon?: React.ReactNode;
  onClick?: MenuClickEventHandler;
}

const Sidebar: FC = () => {
  const [selectedKeys, setSelectedKeys] = useState<string[]>();
  const { logout } = useAppActions(authActions);
  const { pathname } = useLocation();

  useEffect(() => {
    for (const route of [
      RouteNames.event,
      RouteNames.user.index,
      RouteNames.post.index,
      RouteNames.page.index,
      RouteNames.chunk.index,
      RouteNames.redirect.index,
      RouteNames.setting.index,
      RouteNames.seo.index,
      RouteNames.menu.index,
    ]) {
      if (pathname.indexOf(route) === 0) {
        setSelectedKeys([route]);
        return;
      }
    }
    setSelectedKeys([pathname]);
  }, [pathname]);

  const menuItems: (IItem | ReactElement)[] = [
    {
      route: RouteNames.home,
      title: "Панель",
    },
    {
      route: RouteNames.event,
      title: "Календарь",
    },
    {
      route: RouteNames.page.index,
      title: "Страницы",
    },
    {
      route: RouteNames.post.index,
      title: "Записи",
    },
    {
      route: RouteNames.chunk.index,
      title: "Чанки",
    },
    {
      route: RouteNames.menu.index,
      title: "Меню",
    },
    {
      route: RouteNames.redirect.index,
      title: "Редиректы",
    },
    {
      route: RouteNames.seo.index,
      title: "SEO",
    },
    {
      route: RouteNames.setting.index,
      title: "Настройки",
    },
    {
      route: RouteNames.user.index,
      title: "Пользователи",
    },
  ];

  return (
    <Layout.Sider className="app-sidebar" breakpoint="lg" collapsedWidth="0">
      <div className="app-sidebar-top">
        <div className="logo">DL CMS</div>
        <Menu theme="dark" mode="inline" selectedKeys={selectedKeys}>
          {menuItems.map((menuItem) =>
            React.isValidElement(menuItem) ? (
              menuItem
            ) : (
              <Menu.Item
                key={menuItem.route}
                icon={menuItem.icon ?? <RouteIcon route={menuItem.route} />}
                onClick={menuItem.onClick}
              >
                <Link to={menuItem.route}>{menuItem.title}</Link>
              </Menu.Item>
            )
          )}
        </Menu>
      </div>
      <div className="app-sidebar-bottom">
        <Menu theme="dark" mode="inline">
          <Menu.Item
            key="logout"
            icon={<LogoutOutlined />}
            onClick={() => logout()}
          >
            Выйти
          </Menu.Item>
        </Menu>
      </div>
    </Layout.Sider>
  );
};

export default Sidebar;
