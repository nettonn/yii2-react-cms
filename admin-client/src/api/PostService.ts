import RestService from "./RestService";

export default class PostService extends RestService {
  protected name = "post";
  protected url = "/posts";
}

export const postService = new PostService();
