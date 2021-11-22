import { ColumnType } from "antd/lib/table/interface";
import TrueFalseIcon from "./TrueFalseIcon";

export function statusColumn<T>({
  title = "Статус",
  width = 120,
  dataIndex = "status_text",
  key = "status",
  sorter = true,
  filters,
}: ColumnType<T>): ColumnType<T> {
  return {
    title,
    width,
    dataIndex,
    key,
    sorter,
    filters,
    render: (text: any, record: any) => TrueFalseIcon({ value: record[key] }),
    className: "app-data-grid-cell",
    onCell: (record: any, rowIndex: any) => ({
      style: { textAlign: "center", position: "relative" },
    }),
  };
}
