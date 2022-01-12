import RestService from "./RestService";

export default class PostSectionService extends RestService {
  protected name = "post-section";
  protected url = "/post-sections";
}

export const postSectionService = new PostSectionService();
