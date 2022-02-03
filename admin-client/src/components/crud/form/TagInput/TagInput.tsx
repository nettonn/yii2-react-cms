import React, { FC, useEffect, useState } from "react";
import { ValueTextOption } from "../../../../types";
import { Select } from "antd";
import { simpleArrayCompare } from "../../../../utils/functions";

interface TagInputProps {
  value?: string[];
  onChange?: (data: string[]) => void;
  options: ValueTextOption[];
}

const TagInput: FC<TagInputProps> = ({ value, onChange, options }) => {
  if (!value || !onChange) return null;

  return <TagInputInternal tags={value} setTags={onChange} options={options} />;
};

interface TagInputInternalProps {
  tags: string[];
  setTags: (data: string[]) => void;
  options: ValueTextOption[];
}

const TagInputInternal: FC<TagInputInternalProps> = ({
  tags,
  setTags,
  options,
}) => {
  const [selectTags, setSelectTags] = useState<string[]>(tags ?? []);

  useEffect(() => {
    if (tags && !simpleArrayCompare(tags, selectTags)) {
      setSelectTags(tags);
    }
  }, [tags, selectTags]);

  const handleChange = (value: string[]) => {
    const newValues = Object.values(
      Object.fromEntries(
        value.map((s) => [
          s.toLowerCase(),
          s.charAt(0).toUpperCase() + s.toLowerCase().slice(1),
        ])
      )
    );
    setSelectTags(newValues);
    setTags(newValues);
  };

  return (
    <Select<string[]>
      mode="tags"
      style={{ width: "100%" }}
      placeholder="Выберите"
      onChange={handleChange}
      value={selectTags}
      filterOption={(input, option) => {
        if (!option || !option.value) return false;

        const optionValue = option.value.toString().toLowerCase();

        return optionValue.indexOf(input.toLowerCase()) >= 0;
      }}
    >
      {options.map((option) => (
        <Select.Option key={option.text} value={option.text}>
          {option.text}
        </Select.Option>
      ))}
    </Select>
  );
};

export default TagInput;
