import RestService from "./RestService";

export default class VersionService extends RestService {
  protected name = "version";
  protected url = "/versions";
}

export const versionService = new VersionService();
