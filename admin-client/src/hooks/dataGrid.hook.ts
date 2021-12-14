import RestService, { IRestServiceIndexQueryParams } from "../api/RestService";
import { message } from "antd";
import { TablePaginationConfig } from "antd/lib/table/interface";
import { IFiltersParam, IModel, IModelOptions } from "../types";
import { useQuery, useMutation } from "react-query";
import { useAppActions, useAppSelector } from "./redux";
import { useEffect, useState } from "react";
import { appActions } from "../store/reducers/app";
import { queryClient } from "../http/query-client";
import { useLocation, useNavigate } from "react-router-dom";
import { queryStringParse } from "../utils/qs";
import { buildUrl, withoutBaseUrl } from "../utils/functions";
import { DataGridSelector, gridActions } from "../store/reducers/grid";

export default function useDataGrid<
  T extends IModel = IModel,
  M extends IModelOptions = any
>(modelService: RestService, dataGridSelector: DataGridSelector) {
  const dataGridActions = gridActions[dataGridSelector];
  const { pathname: locationPathname, search: locationSearch } = useLocation();
  const [isInit, setIsInit] = useState(false);
  const { currentDataGridSelector } = useAppSelector((state) => state.app);
  const { setCurrentDataGridSelector } = useAppActions(appActions);
  const [allowQueries, setAllowQueries] = useState(false);
  const navigate = useNavigate();

  const {
    currentPage,
    pageSize,
    pageCount,
    dataCount,
    sortField,
    sortDirection,
    searchQuery,
    filters,
  } = useAppSelector((state) => state[dataGridSelector]);

  const {
    setCurrentPage,
    setDataCount,
    setFilters,
    setPageCount,
    setPageSize,
    setSearchQuery,
    setSortDirection,
    setSortField,
  } = useAppActions(dataGridActions);

  useEffect(() => {
    if (locationSearch) {
      const searchParams = queryStringParse(locationSearch);
      if (searchParams) {
        const searchFilters = getFiltersFromSearchParams(searchParams.filters);
        if (searchFilters) {
          setFilters(searchFilters);
        }
        delete searchParams.filters;
      }
      const newUrl = buildUrl(locationPathname, searchParams);
      navigate(withoutBaseUrl(newUrl), { replace: true });
    }
    setAllowQueries(true);
  }, [locationSearch, locationPathname, setFilters, navigate]);

  const {
    data: modelOptions,
    isFetched: modelOptionsIsFetched,
    isFetching: modelOptionsIsFetching,
    isSuccess: modelOptionsIsSuccess,
    error: modelOptionsError,
  } = useQuery(
    modelService.modelOptionsQueryKey(),
    async ({ signal }) => {
      const result = await modelService.modelOptions<M>(signal);
      if (result.success) {
        return result.data;
      }
      message.error(result.error);
      throw new Error(result.error);
    },
    {
      enabled: allowQueries,
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

      if (result.success) {
        if (result.pagination) {
          if (result.pagination.currentPage !== undefined)
            setCurrentPage(result.pagination.currentPage);
          if (result.pagination.totalCount !== undefined)
            setDataCount(result.pagination.totalCount);

          if (result.pagination.perPage !== undefined)
            setPageSize(result.pagination.perPage);

          if (result.pagination.pageCount !== undefined)
            setPageCount(result.pagination.pageCount);
        }
        return result.data;
      }
      message.error(result.error);
      throw new Error(result.error);
    },
    {
      enabled: allowQueries,
      keepPreviousData: true,
    }
  );

  const {
    mutate: deleteHandler,
    isLoading: deleteIsLoading,
    error: deleteError,
  } = useMutation(async (id: number) => {
    const result = await modelService.delete(id);
    if (result.success) {
      queryClient.invalidateQueries(modelService.listQueryKey());
      queryClient.invalidateQueries(modelService.indexQueryKey());
      return true;
    } else {
      message.error(result.error);
    }
  });

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

  const clearAll = () => {
    setFilters(null);
    setSortField(null);
    setSortDirection(null);
    setCurrentPage(1);
    setSearchQuery(null);
  };

  const error = indexError || modelOptionsError || deleteError;

  const isLoading =
    !indexIsFetched || !modelOptionsIsFetched || deleteIsLoading;

  useEffect(() => {
    if (isInit) return;
    if (
      currentDataGridSelector &&
      currentDataGridSelector === dataGridSelector
    ) {
      setIsInit(true);
      return;
    }

    if (
      indexIsSuccess &&
      !indexIsFetching &&
      modelOptionsIsSuccess &&
      !modelOptionsIsFetching
    ) {
      setCurrentDataGridSelector(dataGridSelector);
      setIsInit(true);
    }
  }, [
    isInit,
    indexIsSuccess,
    indexIsFetching,
    modelOptionsIsSuccess,
    modelOptionsIsFetching,
    currentDataGridSelector,
    setCurrentDataGridSelector,
    dataGridSelector,
  ]);

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
  };
}

function getFiltersFromSearchParams(searchFilters: any) {
  if (
    searchFilters &&
    typeof searchFilters === "object" &&
    !Array.isArray(searchFilters)
  ) {
    Object.keys(searchFilters).forEach((key) => {
      if (!Array.isArray(searchFilters[key]))
        searchFilters[key] = [searchFilters[key]];
    });
    return searchFilters;
  }
  return false;
}

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
