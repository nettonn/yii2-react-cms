import { AxiosRequestConfig, AxiosResponse } from "axios";
import { $api, $apiNoAuth } from "../http/axios";
import { IIdentity } from "../models/IIdentity";

const authStorageName = "auth";
const tokenStorageName = "auth-token";
const identityStorageName = "auth-identity";

export default class AuthService {
  protected loginUrl = "/auth/login";
  protected logoutUrl = "/auth/refresh-token";
  protected refreshTokenUrl = "/auth/refresh-token";

  async login(
    email: string,
    password: string
  ): Promise<
    AxiosResponse<{
      identity: IIdentity;
      token: string;
    }>
  > {
    return await $apiNoAuth.post(this.loginUrl, {
      email,
      password,
    });
  }

  async logout() {
    return await $api.delete(this.logoutUrl);
  }

  refreshConfig(): AxiosRequestConfig {
    return {
      url: this.refreshTokenUrl,
      method: "post",
    };
  }

  setAuth(auth: boolean) {
    localStorage.setItem(authStorageName, JSON.stringify(auth));
  }

  getAuth(): boolean | null {
    if (localStorage.getItem(authStorageName)) {
      return JSON.parse(localStorage.getItem(authStorageName) as string);
    }
    return null;
  }

  setToken(token: string) {
    localStorage.setItem(tokenStorageName, token);
  }

  getToken(): string | null {
    return localStorage.getItem(tokenStorageName);
  }

  setIdentity(identity: IIdentity) {
    localStorage.setItem(identityStorageName, JSON.stringify(identity));
  }

  getIdentity(): IIdentity {
    if (localStorage.getItem(identityStorageName))
      return JSON.parse(
        localStorage.getItem(identityStorageName) as string
      ) as IIdentity;
    return {} as IIdentity;
  }

  removeAuthData() {
    localStorage.removeItem(authStorageName);
    localStorage.removeItem(tokenStorageName);
    localStorage.removeItem(identityStorageName);
  }
}

export const authService = new AuthService();
