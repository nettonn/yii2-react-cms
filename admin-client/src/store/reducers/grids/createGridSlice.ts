import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { IFiltersParam } from "../../../types";

export interface DataGridState {
  currentPage: number | null;
  pageCount: number | null;
  pageSize: number | null;
  dataCount: number | null;
  sortField: string | null;
  sortDirection: string | null;
  searchQuery: string | null;
  filters: IFiltersParam | null;
}

const createGridSlice = (name: string) => {
  const initialState: DataGridState = {
    currentPage: null,
    pageCount: null,
    pageSize: null,
    dataCount: null,
    sortField: null,
    sortDirection: null,
    searchQuery: null,
    filters: null,
  };
  return createSlice({
    name,
    initialState,
    reducers: {
      setCurrentPage(state, action: PayloadAction<number>) {
        if (!action.payload || action.payload === 1) {
          state.currentPage = null;
        } else {
          state.currentPage = action.payload;
        }
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
      setSortField(state, action: PayloadAction<string | null>) {
        state.sortField = action.payload;
      },
      setSortDirection(state, action: PayloadAction<string | null>) {
        state.sortDirection = action.payload;
      },
      setSearchQuery(state, action: PayloadAction<string | null>) {
        state.searchQuery = action.payload;
      },
      setFilters(state, action: PayloadAction<IFiltersParam | null>) {
        state.filters = action.payload;
      },
    },
  });
};

export default createGridSlice;
