import { $api } from "../http/api";
import { prepareAxiosConfig, requestErrorHandler } from "../utils/functions";
import { AxiosRequestConfig } from "axios";
import { useState, useCallback } from "react";

export default function useApi(api = $api) {
  const [isLoading, setIsLoading] = useState(false);
  const [isRequestSuccess, setIsRequestSuccess] = useState<boolean | null>(
    null
  );
  const [error, setError] = useState<string | null>(null);

  const request = useCallback(
    async (axiosConfig: AxiosRequestConfig, params = null, data = null) => {
      setIsLoading(true);
      clear();

      const config = prepareAxiosConfig(axiosConfig, params, data);

      try {
        const response = await api.request(config);
        setIsRequestSuccess(true);
        return response.data || true;
      } catch (e: any) {
        const errors = requestErrorHandler(e);
        setError(errors.message ?? null);
      }
      setIsLoading(false);
    },
    [api]
  );

  const clear = () => {
    setIsRequestSuccess(null);
    setError(null);
  };

  return {
    request,
    isLoading,
    isRequestSuccess,
    error,
    clear,
  };
}
