import RestService from "./RestService";

export default class QueueService extends RestService {
  protected name = "queue";
  protected url = "/queues";
}

export const queueService = new QueueService();
