import { IIdentity } from "../../../models/IIdentity";
import { createSlice, PayloadAction } from "@reduxjs/toolkit";

export interface AuthState {
  isAuthChecked: boolean;
  isAuth: boolean;
  identity: IIdentity;
  token: string | null;
}

const initialState: AuthState = {
  isAuthChecked: false,
  isAuth: false,
  identity: {} as IIdentity,
  token: null, // TODO not updated on refresh token
};

const slice = createSlice({
  name: "auth",
  initialState,
  reducers: {
    authorize(
      state,
      action: PayloadAction<{ identity: IIdentity; token: string | null }>
    ) {
      const { identity, token } = action.payload;
      state.identity = identity;
      state.token = token;
      state.isAuth = !!identity && !!token;
      state.isAuthChecked = true;
    },
    clearAuth(state) {
      state.isAuth = false;
      state.identity = {} as IIdentity;
      state.token = null;
      state.isAuthChecked = true;
    },
    setToken(state, action: PayloadAction<string | null>) {
      state.token = action.payload;
    },
  },
});

export const authActions = slice.actions;

export const authReducer = slice.reducer;
