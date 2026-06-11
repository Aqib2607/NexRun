import { createFileRoute, Link, useNavigate } from "@tanstack/react-router";
import { useAuth } from "@/store/auth";
import { useState } from "react";
import { toast } from "sonner";

export const Route = createFileRoute("/register")({
  head: () => ({ meta: [{ title: "Create account — NexRun" }] }),
  component: Register,
});

function Register() {
  const register = useAuth((s) => s.register);
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);

  return (
    <div className="container-nx py-16 md:py-24 max-w-md mx-auto">
      <h1 className="font-display text-4xl font-black">Join NexRun</h1>
      <p className="text-muted-foreground mt-2">Create an account to track orders and save favorites.</p>
      <form
        className="mt-8 space-y-4"
        onSubmit={async (e) => {
          e.preventDefault();
          const fd = new FormData(e.currentTarget);
          setLoading(true);
          await register(String(fd.get("name")), String(fd.get("email")), String(fd.get("password")));
          setLoading(false);
          toast.success("Account created");
          navigate({ to: "/account" });
        }}
      >
        <label className="block">
          <span className="text-sm font-medium">Full name</span>
          <input required name="name" className="mt-1.5 w-full px-4 py-3 rounded-md border border-border bg-background" />
        </label>
        <label className="block">
          <span className="text-sm font-medium">Email</span>
          <input required name="email" type="email" className="mt-1.5 w-full px-4 py-3 rounded-md border border-border bg-background" />
        </label>
        <label className="block">
          <span className="text-sm font-medium">Password</span>
          <input required name="password" type="password" minLength={6} className="mt-1.5 w-full px-4 py-3 rounded-md border border-border bg-background" />
        </label>
        <button disabled={loading} className="w-full bg-primary text-primary-foreground py-3.5 rounded-full font-semibold disabled:opacity-60">
          {loading ? "Creating..." : "Create account"}
        </button>
      </form>
      <p className="mt-6 text-sm text-center text-muted-foreground">
        Already have an account? <Link to="/login" className="underline text-foreground">Sign in</Link>
      </p>
    </div>
  );
}
