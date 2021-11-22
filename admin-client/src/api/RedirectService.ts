import RestService from "./RestService";

export default class RedirectService extends RestService {
  name = "redirect";
  url = "/redirects";
}

export const redirectService = new RedirectService();
