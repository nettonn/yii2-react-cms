import useDataGrid from "../../../hooks/dataGrid.hook";
import { DeleteOutlined, EditOutlined, EyeOutlined } from "@ant-design/icons";
import { Popconfirm, Space, Table, Spin } from "antd";
import Search from "antd/es/input/Search";
import { ColumnsType } from "antd/lib/table/interface";
import React, { FC } from "react";
import { Link, useLocation } from "react-router-dom";
import { IModel } from "../../../types";
import RestService from "../../../api/RestService";
import { useLocalStorage } from "usehooks-ts";

interface DataGridTableProps {
  dataGridHook: ReturnType<typeof useDataGrid>;
  getColumns: (modelOptions: any) => ColumnsType<any>;
  scroll?: { x?: number; y?: number };
  hasUrl?: boolean;
  actionButtons?: (record: any) => React.ReactNode[];
}

const DataGridTable: FC<DataGridTableProps> = ({
  dataGridHook,
  getColumns,
  scroll = { x: 600 },
  hasUrl,
  actionButtons,
}) => {
  const {
    currentPage,
    pageSize,
    dataCount,
    isInit,
    isLoading,
    data,
    error,
    tableChangeHandler,
    searchChangeHandler,
    deleteHandler,
    searchQuery,
    sortField,
    sortDirection,
    filters,
    modelOptions,
  } = dataGridHook;

  const { pathname } = useLocation();

  const [expandedRows, setExpandedRows] = useLocalStorage(
    `${RestService.name}-data-grid-expanded-rows`,
    []
  );

  if (!isInit) return <Spin spinning={true} />;

  if (error) return null;

  const viewButton = (record: IModel) => {
    if (hasUrl) {
      return (
        <a href={record.view_url}>
          <EyeOutlined />
        </a>
      );
    }
    return null;
  };

  const columns = getColumns(modelOptions);

  const actionColumn: any = {
    title: "",
    dataIndex: "",
    key: "x",
    width: 70,
    fixed: "right",
    render: (_: any, record: IModel) => (
      <Space>
        {actionButtons ? actionButtons(record) : null}
        {viewButton(record)}
        <Link to={`${pathname}/${record.id}`}>
          <EditOutlined />
        </Link>
        <Popconfirm title="Удалить?" onConfirm={() => deleteHandler(record.id)}>
          {/*eslint-disable-next-line*/}
          <a>
            <DeleteOutlined />
          </a>
        </Popconfirm>
      </Space>
    ),
  };

  const allColumns = [...columns, actionColumn].map((column: any, index) => {
    const columnKey = column.key ?? column.dataIndex;

    if (sortField && sortDirection && sortField === columnKey) {
      column.sortOrder = sortDirection;
    }
    column.filteredValue =
      filters && filters[columnKey] ? filters[columnKey] : null;

    column.shouldCellUpdate = (record: any, prevRecord: any) => {
      if (index === 0) return true;
      return record !== prevRecord;
    };
    return column;
  });

  return (
    <>
      <Search
        placeholder="Поиск"
        onSearch={searchChangeHandler}
        enterButton
        style={{ marginBottom: "20px" }}
        loading={isLoading}
        allowClear
        defaultValue={searchQuery ?? ""}
      />
      <Table
        columns={allColumns}
        rowKey="id"
        dataSource={data as IModel[]}
        loading={isLoading}
        pagination={{
          total: dataCount ?? undefined,
          current: currentPage ?? 1,
          pageSize: pageSize ?? 0,
          showSizeChanger: false,
          // disabled: !dataCount || dataCount <= pageSize,
          hideOnSinglePage: true,
          size: "default",
        }}
        onChange={tableChangeHandler}
        scroll={scroll}
        showSorterTooltip={false}
        size="small"
        expandable={{
          indentSize: 10,
          defaultExpandedRowKeys: expandedRows,
          onExpandedRowsChange: async (rows: any) => {
            setExpandedRows(rows);
          },
        }}
      />
    </>
  );
};

export default DataGridTable;
