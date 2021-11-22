import React, { FC, useState } from "react";
import AceEditor from "react-ace";
import "ace-builds/src-noconflict/mode-html";
import "ace-builds/src-noconflict/theme-github";
import _uniqueId from "lodash/uniqueId";

interface AceInputProps {
  value?: string;
  onChange?: (value: string) => void;
}

const AceInput: FC<AceInputProps> = ({ value, onChange }) => {
  const [name] = useState(_uniqueId());
  return (
    <AceEditor
      value={value}
      mode="html"
      theme="github"
      onChange={onChange}
      name={`ace-input-${name}`}
      style={{
        width: "100%",
        minHeight: "400px",
      }}
      setOptions={{
        useWorker: false,
      }}
      editorProps={{
        $blockScrolling: true,
        showLineNumbers: true,
      }}
    />
  );
};

export default AceInput;
