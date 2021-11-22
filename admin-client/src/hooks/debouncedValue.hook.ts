import { useState, useRef } from "react";

export default function useDebouncedValue(
  defaultValue: any,
  wait = 300,
  restoreAfter = 0
) {
  const [value, setValue] = useState(defaultValue);
  const [debouncedValue, setDebouncedValue] = useState(defaultValue);
  const timeout = useRef<ReturnType<typeof setTimeout>>();
  const restoreTimeout = useRef<ReturnType<typeof setTimeout>>();

  const debouncedSetValue = (newValue: any, debounce = true) => {
    setValue(newValue);
    timeout.current && clearTimeout(timeout.current);
    if (debounce) {
      restoreTimeout.current && clearTimeout(restoreTimeout.current);
      timeout.current = setTimeout(() => {
        setDebouncedValue(newValue);
        if (restoreAfter) {
          restoreTimeout.current = setTimeout(() => {
            setDebouncedValue(defaultValue);
          }, restoreAfter);
        }
      }, wait);
    } else {
      timeout.current && clearTimeout(timeout.current);
      restoreTimeout.current && clearTimeout(restoreTimeout.current);
      setDebouncedValue(newValue);
    }
  };

  return [value, debouncedSetValue, debouncedValue, setDebouncedValue];
}
