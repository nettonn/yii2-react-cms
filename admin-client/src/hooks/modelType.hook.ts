import { useLayoutEffect, useState } from "react";

export default function useModelType<T = string>(initType?: T) {
  const [type, setType] = useState<T>();

  useLayoutEffect(() => {
    if (initType) setType(initType);
  }, [initType]);

  const typeChangeHandler = (value: T) => {
    setType(value);
  };

  return {
    type,
    typeChangeHandler,
  };
}
