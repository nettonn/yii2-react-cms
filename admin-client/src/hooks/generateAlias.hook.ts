import { FormInstance } from "antd/lib/form/hooks/useForm";
import { useQuery } from "react-query";
import { helperService } from "../api/HelperService";
import { message } from "antd";
import { NamePath } from "rc-field-form/lib/interface";
import { useEffect, useState } from "react";
import { useDebounce } from "use-debounce";

export default function useGenerateAlias(
  form: FormInstance,
  fromFieldName: NamePath,
  toFieldName: NamePath
) {
  const [isFormInit, setIsFormInit] = useState(false);
  const [isAllowed, setIsAllowed] = useState(false);
  const [value, setValue] = useState<string>();

  const [debouncedValue] = useDebounce(value, 300, { maxWait: 1000 });

  const { data } = useQuery(
    [helperService.generateAliasQueryKey, debouncedValue],
    async () => {
      if (!debouncedValue) {
        return "";
      }
      const result = await helperService.generateAlias(debouncedValue);

      if (result.success) {
        return result.data;
      } else {
        message.error(result.error);
        throw new Error(result.error);
      }
    },
    {
      enabled: isAllowed,
      keepPreviousData: true,
    }
  );

  const onFromFieldChange = (fromValue: string) => {
    if (!form.getFieldValue(toFieldName)) {
      setIsAllowed(true);
    }
    setValue(fromValue);
    setIsFormInit(true);
  };

  const onToFieldChange = (toValue: string) => {
    setIsAllowed(false);
  };

  useEffect(() => {
    if (isFormInit && isAllowed) {
      form.setFields([{ name: toFieldName, value: data, errors: [] }]);
    }
  }, [isFormInit, isAllowed, data, form, toFieldName]);

  return [onFromFieldChange, onToFieldChange];
}
