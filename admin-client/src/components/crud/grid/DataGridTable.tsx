import useDataGrid from "../../../hooks/dataGrid.hook";
import { DeleteOutlined, EditOutlined } from "@ant-design/icons";
import { Popconfirm, Space, Table, Spin } from "antd";
import Search from "antd/es/input/Search";
import { ColumnsType } from "antd/lib/table/interface";
import React, { FC } from "react";
import { Link, useLocation } from "react-router-dom";
import { IModel } from "../../../types";
import RestService from "../../../api/RestService";
import useLocalStorage from "../../../hooks/localStorage.hook";

interface DataGridTableProps {
  dataGrid: ReturnType<typeof useDataGrid>;
  columns: ColumnsType<any>;
  scroll?: { x?: number; y?: number };
}

const DataGridTable: FC<DataGridTableProps> = ({
  dataGrid,
  columns,
  scroll = { x: 600 },
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
  } = dataGrid;

  const { pathname } = useLocation();

  const [expandedRows, setExpandedRows] = useLocalStorage(
    `${RestService.name}-data-grid-expanded-rows`,
    []
  );

  const actionColumn: any = {
    title: "",
    dataIndex: "",
    key: "x",
    width: 70,
    fixed: "right",
    render: (_: any, record: IModel) => (
      <Space>
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
    if (filters && filters[columnKey]) {
      column.filteredValue = filters[columnKey];
    }

    column.shouldCellUpdate = (record: any, prevRecord: any) => {
      if (index === 0) return true;
      return record !== prevRecord;
    };
    return column;
  });

  if (!isInit) return <Spin spinning={true} />;

  if (error) return null;

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
          onExpandedRowsChange: async (rows) => {
            setExpandedRows(rows);
          },
        }}
      />
    </>
  );
};

export default DataGridTable;
