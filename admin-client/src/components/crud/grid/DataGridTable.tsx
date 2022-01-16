import useDataGrid from "../../../hooks/dataGrid.hook";
import { DeleteOutlined, EditOutlined, EyeOutlined } from "@ant-design/icons";
import { Popconfirm, Space, Table, Spin, Button, Col, Row, Input } from "antd";
import { ColumnsType } from "antd/lib/table/interface";
import React, { FC, ReactNode, useState } from "react";
import { Link, useLocation } from "react-router-dom";
import { Model } from "../../../types";
import { DEFAULT_ROW_GUTTER } from "../../../utils/constants";
import { useAppActions, useAppSelector } from "../../../hooks/redux";
import { dataGridActions } from "../../../store/reducers/grid/grids";

const Search = Input.Search;

interface DataGridTableProps {
  dataGridHook: ReturnType<typeof useDataGrid>;
  getColumns: (modelOptions: any) => ColumnsType<any>;
  scroll?: { x?: number; y?: number };
  hasUrl?: boolean;
  actionButtons?: (record: any) => ReactNode;
  updatePath?: string; // or use '${location.pathname}/${id}'
}

const DataGridTable: FC<DataGridTableProps> = ({
  dataGridHook,
  getColumns,
  scroll = { x: 600 },
  hasUrl,
  actionButtons,
  updatePath,
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
    clearAll,
    dataGridSelector,
  } = dataGridHook;

  const { pathname } = useLocation();

  const [searchInputValue, setSearchInputValue] = useState(searchQuery ?? "");

  const { expandedRows } = useAppSelector(
    (state) => state.grid[dataGridSelector]
  );

  const { setExpandedRows } = useAppActions(dataGridActions[dataGridSelector]);

  if (!isInit) return <Spin spinning={true} />;

  if (error) return null;

  const viewButton = (record: Model) => {
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
    render: (_: any, record: Model) => (
      <Space>
        {actionButtons ? actionButtons(record) : null}
        {viewButton(record)}
        <Link to={updatePath ?? `${pathname}/${record.id}`}>
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

    column.sortOrder =
      sortField && sortDirection && sortField === columnKey
        ? sortDirection
        : null;

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
      <Row
        gutter={[DEFAULT_ROW_GUTTER, DEFAULT_ROW_GUTTER]}
        style={{ marginBottom: "20px" }}
      >
        <Col flex="auto">
          <Search
            placeholder="Поиск"
            onSearch={searchChangeHandler}
            enterButton
            loading={isLoading}
            allowClear
            value={searchInputValue}
            onChange={(event) => setSearchInputValue(event.target.value)}
          />
        </Col>
        <Col flex="120px">
          <Button
            block
            onClick={() => {
              setSearchInputValue("");
              clearAll();
            }}
          >
            Сброс
          </Button>
        </Col>
      </Row>
      <Table
        columns={allColumns}
        rowKey="id"
        dataSource={data as Model[]}
        loading={isLoading}
        pagination={{
          total: dataCount ?? undefined,
          current: currentPage ?? 1,
          pageSize: pageSize ?? undefined,
          showSizeChanger: false,
          hideOnSinglePage: true,
          size: "default",
        }}
        onChange={tableChangeHandler}
        scroll={scroll}
        showSorterTooltip={false}
        size="small"
        expandable={{
          indentSize: 10,
          expandedRowKeys: expandedRows,
          onExpandedRowsChange: async (rows: any) => {
            setExpandedRows(rows);
          },
        }}
      />
    </>
  );
};

export default DataGridTable;
