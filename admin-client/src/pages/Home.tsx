import React, { FC } from "react";
import { RouteNames } from "../routes";
import RouteIcon from "../components/ui/RouteIcon";
import PageHeader from "../components/ui/PageHeader/PageHeader";
import { Card, List, Space } from "antd";
import { Link } from "react-router-dom";

interface IItem {
  title: string;
  route: string;
  icon?: React.ReactNode;
}

const Home: FC = () => {
  const items: IItem[] = [
    {
      route: RouteNames.event,
      title: "События",
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
    <>
      <PageHeader title="Панель управления" />
      <List //xs={12} sm={12} md={8} lg={6}
        grid={{ gutter: 16, xs: 1, sm: 2, md: 3, lg: 4, xl: 4, xxl: 6 }}
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

export default Home;
