import RestService from "./RestService";

export default class LogService extends RestService {
  protected name = "log";
  protected url = "/logs";
}

export const logService = new LogService();
