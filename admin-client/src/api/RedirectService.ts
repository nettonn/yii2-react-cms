import RestService from "./RestService";

export default class RedirectService extends RestService {
  protected name = "redirect";
  protected url = "/redirects";
}

export const redirectService = new RedirectService();
