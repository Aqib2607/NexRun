import { createFileRoute, Link, useNavigate } from "@tanstack/react-router";
import { useAuth } from "@/store/auth";
import { useState } from "react";
import { toast } from "sonner";

export const Route = createFileRoute("/login")({
  head: () => ({ meta: [{ title: "Sign in — NexRun" }] }),
  component: Login,
});

function Login() {
  const login = useAuth((s) => s.login);
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);

  return (
    <div className="container-nx py-16 md:py-24 max-w-md mx-auto">
      <h1 className="font-display text-4xl font-black">Welcome back</h1>
      <p className="text-muted-foreground mt-2">Sign in to your NexRun account.</p>
      <form
        className="mt-8 space-y-4"
        onSubmit={async (e) => {
          e.preventDefault();
          const fd = new FormData(e.currentTarget);
          setLoading(true);
          await login(String(fd.get("email")), String(fd.get("password")));
          setLoading(false);
          toast.success("Signed in");
          navigate({ to: "/account" });
        }}
      >
        <label className="block">
          <span className="text-sm font-medium">Email</span>
          <input required name="email" type="email" className="mt-1.5 w-full px-4 py-3 rounded-md border border-border bg-background" />
        </label>
        <label className="block">
          <span className="text-sm font-medium">Password</span>
          <input required name="password" type="password" minLength={6} className="mt-1.5 w-full px-4 py-3 rounded-md border border-border bg-background" />
        </label>
        <button disabled={loading} className="w-full bg-primary text-primary-foreground py-3.5 rounded-full font-semibold disabled:opacity-60">
          {loading ? "Signing in..." : "Sign in"}
        </button>
      </form>
      <p className="mt-6 text-sm text-center text-muted-foreground">
        New here? <Link to="/register" className="underline text-foreground">Create an account</Link>
      </p>
    </div>
  );
}
