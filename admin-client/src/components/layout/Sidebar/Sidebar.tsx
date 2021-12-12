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
import { useLocalStorage } from "usehooks-ts";

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
  const { logout } = useAppActions(authActions);
  const { pathname } = useLocation();
  const [storedOpenKeys, setStoredOpenKeys] = useLocalStorage<string[]>(
    `admin-sidebar-open-keys`,
    []
  );
  const [openKeys, setOpenKeys] = useState<string[]>(storedOpenKeys);

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
      RouteNames.version.index,
      RouteNames.log.index,
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
      route: RouteNames.seo.index,
      title: "SEO",
    },
    {
      key: "service",
      title: "Сервис",
      icon: <RouteIcon route={RouteNames.setting.index} />,
      children: [
        {
          hideIcon: true,
          route: RouteNames.redirect.index,
          title: "Редиректы",
        },
        {
          hideIcon: true,
          route: RouteNames.version.index,
          title: "Версии",
        },
        {
          hideIcon: true,
          route: RouteNames.log.index,
          title: "Логи",
        },
        {
          hideIcon: true,
          route: RouteNames.user.index,
          title: "Пользователи",
        },
        {
          hideIcon: true,
          route: RouteNames.setting.index,
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
