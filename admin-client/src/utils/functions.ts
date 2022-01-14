import { ValidationError } from "../types";
import CONSTANTS from "./constants";
import { AxiosRequestConfig } from "axios";
import _isEmpty from "lodash/isEmpty";
import _merge from "lodash/merge";
import { FieldData } from "rc-field-form/es/interface";
import { message } from "antd";
import { queryStringStringify } from "./qs";

export function sleep(ms: number) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

export function simpleCloneObject(object: {}) {
  return JSON.parse(JSON.stringify(object));
}

export function prepareAxiosConfig(
  config: AxiosRequestConfig,
  addConfig?: { params?: {}; data?: {}; headers?: {} }
) {
  const defaultConfig: AxiosRequestConfig = {
    url: "",
    method: "get",
    params: null,
    data: null,
    headers: {},
  };

  const resultConfig = _merge({}, defaultConfig, config, addConfig);

  if (_isEmpty(resultConfig.params)) resultConfig.params = null;
  if (_isEmpty(resultConfig.data)) resultConfig.data = null;

  return resultConfig;
}

export function requestErrorHandler(e: any) {
  interface Errors {
    message?: string;
    status?: number;
    validationErrors?: ValidationError[];
  }
  const result: Errors = {};
  if (e.response) {
    if (e.response.status) {
      result.status = e.response.status;
    }
    if (e.response.status === CONSTANTS.STATUS_VALIDATION_ERROR) {
      result.validationErrors = e.response.data;
    } else if (e.response.status === CONSTANTS.STATUS_NOT_FOUND) {
      result.message = e.message || "Данные не найдены на сервере";
    } else {
      result.message = e.response.data.message || "Неизвестная ошибка";
    }
  } else if (e.request) {
    result.message = "Нет ответа от сервера";
  } else {
    result.message = e.message || "Неизвестная ошибка";
  }
  return result;
}

export function prepareAntdValidationErrors(
  validationErrors: ValidationError[]
) {
  const fields: FieldData[] = [];
  for (const { field, message } of validationErrors) {
    fields.push({
      name: field,
      errors: [message],
    });
  }
  return fields;
}

export function sortObjectByIds<T extends { id: ID }, ID = number>(
  ids: ID[],
  object: T[]
) {
  const sortedObject: T[] = [];
  // const notFound: T[] = [];
  for (const id of ids) {
    const found = object.find((o) => o.id === id);
    if (found) {
      sortedObject.push(found);
    }
  }
  // TODO with not found
  return sortedObject;
}

export function stopPropagation(e: any) {
  e.stopPropagation();
}

export function withStopPropagation(callback: Function) {
  return (e: any) => {
    e.stopPropagation();
    callback();
  };
}

export function simpleArrayCompare<T extends number | string>(
  array1?: T[] | null,
  array2?: T[] | null
) {
  if (array1 === array2) return true;

  if (!array1 && !array2) return true;

  if (!array1 || !array2) return false;

  return (
    array1.length === array2.length &&
    array1.every((value, index) => value === array2[index])
  );
}

export function removeObjectEmpty(object: object) {
  return Object.fromEntries(
    Object.entries(object).filter(([_, v]) => v != null)
  );
}

export function stringReplace(
  string: string,
  replaces: { [key: string]: string | number | undefined }
) {
  Object.keys(replaces).forEach((find) => {
    let replace = replaces[find];
    if (!replace) return;
    if (typeof replace === "number") {
      replace = replace.toString();
    }
    string = string.replace(new RegExp(find), replace);
  });

  return string;
}

export function openNewWindow(url?: string) {
  if (!url) return;
  window.open(url, "_blank")?.focus();
}

export function logMessage(link: string) {
  message.info({
    content: `Debug link: ${link}`,
    duration: 5,
    onClick: () => openNewWindow(process.env.REACT_APP_HOST + link),
  });
}

export function withoutBaseUrl(url: string) {
  const baseurl = process.env.PUBLIC_URL;
  if (baseurl.length === 0 || url.length === 0 || url.indexOf(baseurl) !== 0) {
    return url;
  }
  return url.substring(baseurl.length);
}

export function buildUrl(path: string, params?: {}) {
  if (params) {
    const queryString = queryStringStringify(params);
    if (queryString) {
      path = path + "?" + queryString;
    }
  }
  return path;
}
