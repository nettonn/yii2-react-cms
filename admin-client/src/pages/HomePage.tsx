import React, { FC } from "react";
import { routeNames } from "../routes";
import RouteIcon from "../components/ui/RouteIcon";
import { Card, List, Space, Typography } from "antd";
import { Link } from "react-router-dom";
import { DEFAULT_ROW_GUTTER } from "../utils/constants";

const Title = Typography.Title;

interface IItem {
  title: string;
  route: string;
  icon?: React.ReactNode;
}

const HomePage: FC = () => {
  const items: IItem[] = [
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
      route: routeNames.order.index,
      title: "Заявки",
    },
    {
      route: routeNames.seo.index,
      title: "SEO",
    },
  ];

  const serviceItems: IItem[] = [
    {
      route: routeNames.redirect.index,
      title: "Редиректы",
    },
    {
      route: routeNames.version.index,
      title: "Версии",
    },
    {
      route: routeNames.queue.index,
      title: "Задачи",
    },
    {
      route: routeNames.log.index,
      title: "Логи",
    },
    {
      route: routeNames.setting.index,
      title: "Настройки",
    },
    {
      route: routeNames.user.index,
      title: "Пользователи",
    },
  ];

  const renderItem = (item: IItem) => {
    return (
      <List.Item>
        <Link to={item.route}>
          <Card hoverable>
            <Space>
              {item.icon ?? <RouteIcon route={item.route} />}
              {item.title}
            </Space>
          </Card>
        </Link>
      </List.Item>
    );
  };

  return (
    <>
      <Title level={3}>Панель управления</Title>
      <List //xs={12} sm={12} md={8} lg={6}
        grid={{
          gutter: DEFAULT_ROW_GUTTER,
          xs: 1,
          sm: 2,
          md: 3,
          lg: 4,
          xl: 4,
          xxl: 6,
        }}
        dataSource={items}
        renderItem={(item) => renderItem(item)}
      />
      <Title level={3}>Сервис</Title>
      <List //xs={12} sm={12} md={8} lg={6}
        grid={{
          gutter: DEFAULT_ROW_GUTTER,
          xs: 1,
          sm: 2,
          md: 3,
          lg: 4,
          xl: 4,
          xxl: 6,
        }}
        dataSource={serviceItems}
        renderItem={(item) => renderItem(item)}
      />
    </>
  );
};

export default HomePage;
