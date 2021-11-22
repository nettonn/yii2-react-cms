import React from "react";

if (process.env.NODE_ENV === "development1") {
  const whyDidYouRender = require("@welldone-software/why-did-you-render");
  whyDidYouRender(React, {
    trackAllPureComponents: true,
    // onlyLogs: true,
    collapseGroups: true,
    // exclude: [/^Cell/],
    // logOnDifferentValues: true,
  });
}
