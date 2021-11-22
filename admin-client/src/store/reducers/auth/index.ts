import { IIdentity } from "../../../models/IIdentity";
import { IValidationErrorType } from "../../../types";
import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { authActionCreators } from "./action-creators";
import { authService } from "../../../api/AuthService";

export interface AuthState {
  isAuthChecked: boolean;
  isAuth: boolean;
  identity: IIdentity;
  error: string | null;
  validationErrors: IValidationErrorType[] | null;
  token: string | null;
}

const initialState: AuthState = {
  isAuthChecked: false,
  isAuth: false,
  identity: {} as IIdentity,
  error: null,
  validationErrors: null,
  token: null,
};

export const authSlice = createSlice({
  name: "auth",
  initialState,
  reducers: {
    setIsAuth(state, action: PayloadAction<boolean>) {
      state.isAuth = action.payload;
    },
    setIsAuthChecked(state, action: PayloadAction<boolean>) {
      state.isAuthChecked = action.payload;
    },
    setIdentity(state, action: PayloadAction<IIdentity>) {
      state.identity = action.payload;
    },
    setError(state, action: PayloadAction<string | null>) {
      state.error = action.payload;
    },
    setValidationErrors(
      state,
      action: PayloadAction<IValidationErrorType[] | null>
    ) {
      state.validationErrors = action.payload;
    },
    setToken(state, action: PayloadAction<string | null>) {
      state.token = action.payload;
    },
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
  },
  extraReducers: {
    [authActionCreators.login.pending.type]: (state, action) => {
      state.error = null;
      state.validationErrors = null;
    },
    [authActionCreators.login.fulfilled.type]: (state, action) => {
      authService.setAuth(true);
      authService.setIdentity(action.payload.identity);
      authService.setToken(action.payload.token);
      state.identity = action.payload.identity;
      state.token = action.payload.token;
      state.isAuth = true;
      state.isAuthChecked = true;
    },
    [authActionCreators.login.rejected.type]: (state, action) => {
      if (action.payload) {
        state.validationErrors = action.payload;
      } else {
        state.error = action.error.message;
      }
    },
    [authActionCreators.logout.fulfilled.type]: (state, action) => {
      authService.removeAuthData();
      state.isAuth = false;
      state.identity = {} as IIdentity;
      state.token = null;
    },
    [authActionCreators.logout.rejected.type]: (state, action) => {
      state.error = action.error.message;
    },
  },
});

export const authActions = { ...authSlice.actions, ...authActionCreators };

export const authReducer = authSlice.reducer;
