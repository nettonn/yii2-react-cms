import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import useDataGrid from "../../hooks/dataGrid.hook";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { RouteNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { ColumnsType } from "antd/lib/table/interface";
import { Link } from "react-router-dom";
import { ISetting } from "../../models/ISetting";
import { settingService } from "../../api/SettingService";
import { settingGridActions } from "../../store/reducers/grids/settingGrid";

const modelRoutes = RouteNames.setting;

const Settings: FC = () => {
  const dataGrid = useDataGrid<ISetting>(
    settingService,
    "settingGrid",
    settingGridActions
  );

  const columns: ColumnsType<ISetting> = [
    {
      title: "Id",
      dataIndex: "id",
      sorter: true,
      width: 80,
    },
    {
      title: "Название",
      dataIndex: "name",
      sorter: true,
      // filters: ,
      ellipsis: true,
      render: (text: any, record: ISetting) => {
        return (
          <Link to={modelRoutes.view.replace(/:id/, record.id.toString())}>
            {text}
          </Link>
        );
      },
    },
    {
      title: "Ключ",
      dataIndex: "key",
      sorter: true,
      // width: 120,
    },
    {
      title: "Значение",
      dataIndex: "value",
      // sorter: true,
      // width: 200,
    },
    {
      title: "Изменено",
      dataIndex: "updated_at_date",
      key: "updated_at",
      sorter: true,
      width: 120,
    },
  ];

  return (
    <>
      <PageHeader title="Настройки" backPath={RouteNames.home} />

      <DataGridTable dataGrid={dataGrid} columns={columns} />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default Settings;
