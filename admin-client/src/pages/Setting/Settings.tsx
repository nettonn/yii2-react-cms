import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { RouteNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { ColumnsType } from "antd/lib/table/interface";
import { Link } from "react-router-dom";
import { ISetting, ISettingModelOptions } from "../../models/ISetting";
import { settingService } from "../../api/SettingService";
import { settingGridActions } from "../../store/reducers/grids/settingGrid";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = RouteNames.setting;

const Settings: FC = () => {
  const dataGridHook = useDataGrid(
    settingService,
    "settingGrid",
    settingGridActions
  );

  const getColumns = (
    modelOptions: ISettingModelOptions
  ): ColumnsType<ISetting> => [
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
        return <Link to={modelRoutes.updateUrl(record.id)}>{text}</Link>;
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

      <DataGridTable dataGridHook={dataGridHook} getColumns={getColumns} />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default Settings;
