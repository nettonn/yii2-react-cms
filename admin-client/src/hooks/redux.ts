import { useDispatch, TypedUseSelectorHook, useSelector } from "react-redux";
import { AppDispatch, RootState } from "../store";
import { bindActionCreators } from "redux";
import { useMemo } from "react";

export const useAppDispatch = () => useDispatch<AppDispatch>();

export const useAppSelector: TypedUseSelectorHook<RootState> = useSelector;

export function useAppActions<T>(actions: T, deps?: any): T {
  const dispatch = useAppDispatch();
  return useMemo(
    () => {
      if (Array.isArray(actions)) {
        return actions.map((a) => bindActionCreators(a, dispatch));
      }
      return bindActionCreators(actions as any, dispatch);
    },
    // eslint-disable-next-line
    deps ? [dispatch, ...deps] : [dispatch]
  ) as T;
}
