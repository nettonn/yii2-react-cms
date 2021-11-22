import { useRef, useEffect, useCallback } from "react";

export function useIsMounted(): () => boolean {
  const isMounted = useRef(false);

  useEffect(() => {
    isMounted.current = true;
    return () => {
      isMounted.current = false;
    };
  }, []);

  return useCallback((): boolean => isMounted.current, []);
}
