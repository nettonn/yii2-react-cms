import { QueryClient } from "react-query";

export const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      useErrorBoundary: true,
      // retry: false,
      refetchOnWindowFocus: false,
      // cacheTime: 0,
    },
  },
});
