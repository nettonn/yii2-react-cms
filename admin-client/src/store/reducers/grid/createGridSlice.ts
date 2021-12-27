import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { IFiltersParam } from "../../../types";

interface IPaginationFields {
  currentPage: number | null;
  pageCount: number | null;
  pageSize: number | null;
  dataCount: number | null;
}

export interface DataGridState extends IPaginationFields {
  sortField: string | null;
  sortDirection: string | null;
  searchQuery: string | null;
  filters: IFiltersParam | null;
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
      setPagination(state, action: PayloadAction<Partial<IPaginationFields>>) {
        const fieldNames = Object.keys(action.payload) as Array<
          Extract<IPaginationFields, string>
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
      setFilters(state, action: PayloadAction<IFiltersParam | null>) {
        state.filters = action.payload;
      },
    },
  });
};

export default createGridSlice;
