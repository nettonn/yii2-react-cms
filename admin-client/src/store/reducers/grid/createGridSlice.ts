import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { FilterParams } from "../../../types";

interface PaginationFields {
  currentPage: number | null;
  pageCount: number | null;
  pageSize: number | null;
  dataCount: number | null;
}

export interface DataGridState extends PaginationFields {
  sortField: string | null;
  sortDirection: string | null;
  searchQuery: string | null;
  filters: FilterParams | null;
  expandedRows: number[];
}

const createGridSlice = <Name extends string = string>(name: Name) => {
  const initialState: DataGridState = {
    currentPage: 1,
    pageCount: null,
    pageSize: null,
    dataCount: null,
    sortField: null,
    sortDirection: null,
    searchQuery: null,
    filters: null,
    expandedRows: [],
  };
  return createSlice({
    name,
    initialState,
    reducers: {
      setCurrentPage(state, action: PayloadAction<number>) {
        state.currentPage = action.payload;
      },
      setPageCount(state, action: PayloadAction<number>) {
        state.pageCount = action.payload;
      },
      setPageSize(state, action: PayloadAction<number>) {
        state.pageSize = action.payload;
      },
      setDataCount(state, action: PayloadAction<number>) {
        state.dataCount = action.payload;
      },
      setPagination(state, action: PayloadAction<Partial<PaginationFields>>) {
        const fieldNames = Object.keys(action.payload) as Array<
          Extract<PaginationFields, string>
        >;
        fieldNames.forEach((key) => {
          if (action.payload[key] === undefined) return;
          state[key] = action.payload[key];
        });
      },
      setSortField(state, action: PayloadAction<string | null>) {
        state.sortField = action.payload;
      },
      setSortDirection(state, action: PayloadAction<string | null>) {
        state.sortDirection = action.payload;
      },
      setSearchQuery(state, action: PayloadAction<string | null>) {
        state.searchQuery = action.payload;
      },
      setFilters(state, action: PayloadAction<FilterParams | null>) {
        state.filters = action.payload;
      },
      setExpandedRows(state, action: PayloadAction<number[]>) {
        state.expandedRows = action.payload;
      },
      reset(state) {
        state.currentPage = initialState.currentPage;
        state.sortField = initialState.sortField;
        state.sortDirection = initialState.sortDirection;
        state.filters = initialState.filters;
        state.searchQuery = initialState.searchQuery;
      },
    },
  });
};

export default createGridSlice;
