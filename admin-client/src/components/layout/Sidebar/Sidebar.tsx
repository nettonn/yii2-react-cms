import React, { FC, useState, useEffect, ReactElement } from "react";
import { Layout, Menu } from "antd";
import "./Sidebar.css";
import { Link, useLocation } from "react-router-dom";
import { routeNames } from "../../../routes";
import RouteIcon from "../../ui/RouteIcon";
import { MenuClickEventHandler } from "rc-menu/lib/interface";
import { LogoutOutlined } from "@ant-design/icons";
import { useLocalStorage } from "usehooks-ts";
import useLogout from "../../../hooks/logout.hook";

interface IItem {
  title: string;
  key?: string;
  route?: string;
  hideIcon?: boolean;
  icon?: React.ReactNode;
  onClick?: MenuClickEventHandler;
  children?: IItem[];
}

const Sidebar: FC = () => {
  const [selectedKeys, setSelectedKeys] = useState<string[]>();
  const { logout, isLoading: logoutIsLoading } = useLogout();
  const { pathname } = useLocation();
  const [storedOpenKeys, setStoredOpenKeys] = useLocalStorage<string[]>(
    `admin-sidebar-open-keys`,
    []
  );
  const [openKeys, setOpenKeys] = useState<string[]>(storedOpenKeys);

  useEffect(() => {
    for (const route of [
      routeNames.user.index,
      routeNames.post.index,
      routeNames.page.index,
      routeNames.chunk.index,
      routeNames.redirect.index,
      routeNames.setting.index,
      routeNames.seo.index,
      routeNames.menu.index,
      routeNames.version.index,
      routeNames.log.index,
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
      route: routeNames.home,
      title: "Панель",
    },
    {
      route: routeNames.page.index,
      title: "Страницы",
    },
    {
      route: routeNames.post.index,
      title: "Записи",
    },
    {
      route: routeNames.chunk.index,
      title: "Чанки",
    },
    {
      route: routeNames.menu.index,
      title: "Меню",
    },
    {
      route: routeNames.seo.index,
      title: "SEO",
    },
    {
      key: "service",
      title: "Сервис",
      icon: <RouteIcon route={routeNames.setting.index} />,
      children: [
        {
          hideIcon: true,
          route: routeNames.redirect.index,
          title: "Редиректы",
        },
        {
          hideIcon: true,
          route: routeNames.version.index,
          title: "Версии",
        },
        {
          hideIcon: true,
          route: routeNames.queue.index,
          title: "Задачи",
        },
        {
          hideIcon: true,
          route: routeNames.log.index,
          title: "Логи",
        },
        {
          hideIcon: true,
          route: routeNames.user.index,
          title: "Пользователи",
        },
        {
          hideIcon: true,
          route: routeNames.setting.index,
          title: "Настройки",
        },
      ],
    },
  ];

  const collapseHandler = (collapsed: boolean) => {
    if (collapsed) {
      setOpenKeys([]);
    } else {
      setOpenKeys([...storedOpenKeys]);
    }
  };

  const subMenuClickHandler = (e: any) => {
    let newOpenKeys = [];
    if (openKeys.find((i) => i === e.key)) {
      newOpenKeys = openKeys.filter((i) => i !== e.key);
    } else {
      newOpenKeys = [...openKeys, e.key];
    }
    setOpenKeys([...newOpenKeys]);
    setStoredOpenKeys([...newOpenKeys]);
  };

  const getMenuItemIcon = (menuItem: IItem) => {
    if (menuItem.hideIcon) return null;

    if (menuItem.icon) {
      return menuItem.icon;
    }
    if (menuItem.route) {
      return <RouteIcon route={menuItem.route} />;
    }
  };

  const getMenuItemText = (menuItem: IItem) => {
    if (menuItem.route)
      return <Link to={menuItem.route}>{menuItem.title}</Link>;
    return menuItem.title;
  };

  const getMenuItemKey = (menuItem: IItem) => {
    if (menuItem.route) return menuItem.route;
    if (menuItem.key) return menuItem.key;
    return menuItem.title;
  };

  const renderMenuItem = (menuItem: IItem | ReactElement) => {
    if (React.isValidElement(menuItem)) {
      return menuItem;
    }

    if (menuItem.children && menuItem.children.length) {
      return (
        <Menu.SubMenu
          key={getMenuItemKey(menuItem)}
          icon={getMenuItemIcon(menuItem)}
          title={menuItem.title}
          onTitleClick={subMenuClickHandler}
        >
          {menuItem.children.map((child) => renderMenuItem(child))}
        </Menu.SubMenu>
      );
    }

    return (
      <Menu.Item
        key={getMenuItemKey(menuItem)}
        icon={getMenuItemIcon(menuItem)}
        onClick={menuItem.onClick}
      >
        {getMenuItemText(menuItem)}
      </Menu.Item>
    );
  };

  return (
    <Layout.Sider
      className="app-sidebar"
      breakpoint="lg"
      collapsedWidth="0"
      onCollapse={collapseHandler}
    >
      <div className="app-sidebar-top">
        <div className="logo">DL CMS</div>
        <Menu
          theme="dark"
          mode="inline"
          selectedKeys={selectedKeys}
          openKeys={openKeys}
        >
          {menuItems.map((menuItem) => renderMenuItem(menuItem))}
        </Menu>
      </div>
      <div className="app-sidebar-bottom">
        <Menu theme="dark" mode="inline">
          <Menu.Item
            key="logout"
            icon={<LogoutOutlined />}
            disabled={logoutIsLoading}
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
