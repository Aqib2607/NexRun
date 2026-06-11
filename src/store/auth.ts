import { create } from "zustand";
import { persist } from "zustand/middleware";

export type User = { id: string; name: string; email: string };

type AuthState = {
  user: User | null;
  login: (email: string, password: string) => Promise<void>;
  register: (name: string, email: string, password: string) => Promise<void>;
  logout: () => void;
};

export const useAuth = create<AuthState>()(
  persist(
    (set) => ({
      user: null,
      login: async (email) => {
        await new Promise((r) => setTimeout(r, 400));
        set({ user: { id: "u_1", name: email.split("@")[0], email } });
      },
      register: async (name, email) => {
        await new Promise((r) => setTimeout(r, 400));
        set({ user: { id: "u_1", name, email } });
      },
      logout: () => set({ user: null }),
    }),
    { name: "nexrun-auth" },
  ),
);
