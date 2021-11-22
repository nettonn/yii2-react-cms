import React, { FC } from "react";
import "./PageActions.css";

interface PageActionsProps {
  content?: React.ReactNode;
}

const PageActions: FC<PageActionsProps> = ({ content }) => {
  if (!content) return null;
  return (
    <div className="app-page-actions">
      <div className="app-page-actions-wrapper">{content}</div>
    </div>
  );
};

export default PageActions;
