import "react";
import { render, screen } from "@testing-library/react";
import { describe, it, expect } from "vitest";
import { Button } from "./button";

describe("Button", () => {
  it("renders the button with provided text", () => {
    render(<Button>Click Me</Button>);
    expect(screen.getByRole("button", { name: /click me/i })).toBeInTheDocument();
  });

  it("handles custom className", () => {
    render(<Button className="custom-class">Click Me</Button>);
    expect(screen.getByRole("button")).toHaveClass("custom-class");
  });
});
