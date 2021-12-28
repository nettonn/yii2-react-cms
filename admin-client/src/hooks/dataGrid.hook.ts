import RestService, { IRestServiceIndexQueryParams } from "../api/RestService";
import { TablePaginationConfig } from "antd/lib/table/interface";
import { IFiltersParam, IModel, IModelOptions } from "../types";
import { useQuery, useMutation } from "react-query";
import { useAppActions, useAppSelector } from "./redux";
import { useEffect, useState } from "react";
import { requestErrorHandler } from "../utils/functions";
import {
  DataGridSelector,
  dataGridActions,
} from "../store/reducers/grid/grids";
import { message } from "antd";

export default function useDataGrid<
  T extends IModel = IModel,
  M extends IModelOptions = any
>(modelService: RestService, dataGridSelector: DataGridSelector) {
  const [isInit, setIsInit] = useState(false);

  const {
    currentPage,
    pageSize,
    pageCount,
    dataCount,
    sortField,
    sortDirection,
    searchQuery,
    filters,
  } = useAppSelector((state) => state.grid[dataGridSelector]);

  const {
    setCurrentPage,
    setFilters,
    setSearchQuery,
    setSortDirection,
    setSortField,
    setPagination,
    reset: clearAll,
  } = useAppActions(dataGridActions[dataGridSelector]);

  const {
    data: modelOptions,
    isFetched: modelOptionsIsFetched,
    isFetching: modelOptionsIsFetching,
    isSuccess: modelOptionsIsSuccess,
    error: modelOptionsError,
  } = useQuery(
    modelService.modelOptionsQueryKey(),
    async ({ signal }) => {
      return await modelService.modelOptions<M>(signal);
    },
    {
      refetchOnMount: false,
    }
  );

  const {
    data,
    isFetched: indexIsFetched,
    isFetching: indexIsFetching,
    isSuccess: indexIsSuccess,
    error: indexError,
  } = useQuery(
    [
      modelService.indexQueryKey(),
      { currentPage, sortField, sortDirection, searchQuery, filters },
    ],
    async ({ signal }) => {
      const params: IRestServiceIndexQueryParams = {};

      if (currentPage) params.page = currentPage;
      if (sortField) params.sortField = sortField;
      if (sortDirection) params.sortDirection = sortDirection;
      if (searchQuery) params.search = searchQuery;
      if (filters) params.filters = filters;

      const result = await modelService.index<T>(params, signal);
      if (result.pagination) {
        setPagination({
          currentPage: result.pagination.currentPage,
          dataCount: result.pagination.totalCount,
          pageSize: result.pagination.perPage,
          pageCount: result.pagination.pageCount,
        });
      }
      return result.data;
    },
    {
      keepPreviousData: true,
    }
  );

  const { mutate: deleteHandler, isLoading: deleteIsLoading } = useMutation(
    async (id: number) => {
      await modelService.delete(id);
      return true;
    },
    {
      onError: (e) => {
        const errors = requestErrorHandler(e);
        message.error(errors.message);
      },
    }
  );

  const tableChangeHandler = (
    tablePagination: TablePaginationConfig,
    tableFilters: any,
    tableSorter: any
  ) => {
    if (tablePagination.current) setCurrentPage(tablePagination.current);
    else setCurrentPage(1);

    if ((tableSorter.columnKey || tableSorter.field) && tableSorter.order) {
      setSortField(tableSorter.columnKey || tableSorter.field);
      setSortDirection(tableSorter.order);
    } else {
      setSortField(null);
      setSortDirection(null);
    }

    const filterParams = parseTableFilters(tableFilters);
    if (filterParams) {
      setFilters(filterParams);
    } else {
      setFilters(null);
    }
  };

  const searchChangeHandler = async (value: string) => {
    setSearchQuery(value);
    setCurrentPage(1);
  };

  const error = indexError || modelOptionsError;

  const isLoading =
    !indexIsFetched || !modelOptionsIsFetched || deleteIsLoading;

  const isLoaded =
    indexIsSuccess &&
    modelOptionsIsSuccess &&
    !indexIsFetching &&
    !modelOptionsIsFetching;

  useEffect(() => {
    if (isInit) return;

    if (isLoaded) {
      setIsInit(true);
    }
  }, [isInit, isLoaded]);

  return {
    data,
    modelOptions,
    currentPage,
    pageSize,
    dataCount,
    pageCount,
    isInit,
    isLoading,
    error,
    searchQuery,
    sortField,
    sortDirection,
    filters,
    searchChangeHandler,
    tableChangeHandler,
    deleteHandler,
    clearAll,
    dataGridSelector,
  };
}

// function getFiltersFromSearchParams(searchFilters: any) {
//   if (
//     searchFilters &&
//     typeof searchFilters === "object" &&
//     !Array.isArray(searchFilters)
//   ) {
//     Object.keys(searchFilters).forEach((key) => {
//       if (!Array.isArray(searchFilters[key]))
//         searchFilters[key] = [searchFilters[key]];
//     });
//     return searchFilters;
//   }
//   return false;
// }

function parseTableFilters(tableFilters: any) {
  if (!tableFilters) return false;

  const filterKeys = Object.keys(tableFilters).filter(
    (key: string) => tableFilters[key]
  );

  if (!filterKeys.length) return false;

  const filterParams = {} as IFiltersParam;
  for (const key of filterKeys) {
    filterParams[key] = tableFilters[key];
  }
  return filterParams;
}
