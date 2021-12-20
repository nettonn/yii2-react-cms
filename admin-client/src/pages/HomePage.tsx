import React, { FC } from "react";
import { routeNames } from "../routes";
import RouteIcon from "../components/ui/RouteIcon";
import PageHeader from "../components/ui/PageHeader/PageHeader";
import { Card, List, Space } from "antd";
import { Link } from "react-router-dom";
import { DEFAULT_ROW_GUTTER } from "../utils/constants";

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
      route: routeNames.seo.index,
      title: "SEO",
    },
    {
      route: routeNames.redirect.index,
      title: "Редиректы",
    },
    {
      route: routeNames.log.index,
      title: "Логи",
    },
    {
      route: routeNames.version.index,
      title: "Версии",
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

  return (
    <>
      <PageHeader title="Панель управления" />
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
        renderItem={(item) => (
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
        )}
      />
    </>
  );
};

export default HomePage;
