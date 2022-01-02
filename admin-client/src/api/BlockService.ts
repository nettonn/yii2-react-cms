import RestService from "./RestService";

export default class BlockService extends RestService {
  protected name = "block";
  protected url = "/blocks";
}
export const blockService = new BlockService();
