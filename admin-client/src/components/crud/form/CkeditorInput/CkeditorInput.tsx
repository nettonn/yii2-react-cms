import React, { FC } from "react";
import { CKEditor } from "@ckeditor/ckeditor5-react";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic/build/ckeditor.js";
import { UploadAdapterPlugin } from "./UploadAdapter";

interface CkeditorInputProps {
  value?: string;
  onChange?: (data: string) => void;
}

const CkeditorInput: FC<CkeditorInputProps> = ({ value, onChange }) => {
  return (
    <CKEditor
      editor={ClassicEditor}
      data={value}
      config={{
        extraPlugins: [UploadAdapterPlugin],
        link: {
          // Automatically add target="_blank" and rel="noopener noreferrer" to all external links.
          addTargetToExternalLinks: true,

          // Let the users control the "download" attribute of each link.
          decorators: [
            {
              mode: "manual",
              label: "Downloadable",
              attributes: {
                download: "download",
              },
            },
          ],
        },
        language: "ru",
        // toolbar: {
        //   items: [
        //     "heading",
        //     "|",
        //     "alignment",
        //     "bold",
        //     "italic",
        //     "link",
        //     "bulletedList",
        //     "numberedList",
        //     "uploadImage",
        //     "blockQuote",
        //     "undo",
        //     "redo",
        //   ],
        // },
      }}
      // onReady={(editor) => {
      //   You can store the "editor" and use when it is needed.
      // console.log("Editor is ready to use!", editor);
      // }}
      onChange={(event: any, editor: any) => {
        const data: string = editor.getData();
        onChange && onChange(data);
        // console.log({ event, editor, data });
      }}
      // onBlur={(event, editor) => {
      //   console.log("Blur.", editor);
      // }}
      // onFocus={(event, editor) => {
      //   console.log("Focus.", editor);
      // }}
    />
  );
};

export default CkeditorInput;
