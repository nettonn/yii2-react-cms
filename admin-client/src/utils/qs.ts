export const qs = require("qs");

export const qsStringifyConfig = {
  encodeValuesOnly: true,
  arrayFormat: "comma",
};

export const qsParseConfig = {
  comma: true,
  ignoreQueryPrefix: true,
};

export function queryStringStringify(params: {}) {
  return qs.stringify(params, qsStringifyConfig);
}

export function queryStringParse(string: string) {
  return qs.parse(string, qsParseConfig);
}
