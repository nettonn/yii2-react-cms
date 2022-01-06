import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { ColumnsType } from "antd/lib/table/interface";
import { Link } from "react-router-dom";
import { ISetting, ISettingModelOptions } from "../../models/ISetting";
import { settingService } from "../../api/SettingService";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = routeNames.setting;

const SettingsPage: FC = () => {
  const dataGridHook = useDataGrid<ISetting, ISettingModelOptions>(
    settingService,
    "setting"
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
      render: (value, record) => {
        return <Link to={modelRoutes.updateUrl(record.id)}>{value}</Link>;
      },
    },
    {
      title: "Ключ",
      dataIndex: "key",
      sorter: true,
      // width: 120,
    },
    {
      title: "Тип",
      dataIndex: "type_label",
      key: "type",
      sorter: true,
      filters: modelOptions.type,
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
      <PageHeader title="Настройки" backPath={routeNames.home} />

      <DataGridTable dataGridHook={dataGridHook} getColumns={getColumns} />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default SettingsPage;
