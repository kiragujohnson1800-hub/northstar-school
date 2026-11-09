(() => {
  const messageBox = document.querySelector("#form-message");
  const params = new URLSearchParams(window.location.search);
  const messages = {
    invalid: { text: "Check your details and try again.", style: "border-rose-900 bg-rose-950/60 text-rose-200" },
    duplicate: { text: "An account already uses that email address.", style: "border-rose-900 bg-rose-950/60 text-rose-200" },
    session: { text: "Your session expired. Refresh the page and try again.", style: "border-amber-900 bg-amber-950/60 text-amber-200" },
    unavailable: { text: "The service is temporarily unavailable. Please try again shortly.", style: "border-amber-900 bg-amber-950/60 text-amber-200" },
  };

  if (messageBox) {
    let notice = messages[params.get("error")];
    if (params.get("registered") === "1") {
      notice = { text: "Your student account is ready. Sign in with your new credentials.", style: "border-emerald-900 bg-emerald-950/60 text-emerald-200" };
    } else if (params.get("logged_out") === "1") {
      notice = { text: "You have signed out.", style: "border-emerald-900 bg-emerald-950/60 text-emerald-200" };
    }
    if (notice) {
      messageBox.textContent = notice.text;
      messageBox.classList.add(...notice.style.split(" "));
      messageBox.classList.remove("hidden");
    }
  }

  const csrfInput = document.querySelector("[data-csrf-token]");
  if (csrfInput) {
    const submitButton = csrfInput.form?.querySelector('[type="submit"]');
    if (submitButton) submitButton.disabled = true;
    fetch("api/add-user.php?action=csrf", { credentials: "same-origin", headers: { Accept: "application/json" } })
      .then(async (response) => {
        if (!response.ok) throw new Error("Could not initialize the secure form.");
        return response.json();
      })
      .then((data) => {
        csrfInput.value = data.csrf_token;
        if (submitButton) submitButton.disabled = false;
      })
      .catch(() => {
        if (messageBox) {
          messageBox.textContent = "Could not initialize a secure form. Refresh the page and try again.";
          messageBox.classList.add("border-amber-900", "bg-amber-950/60", "text-amber-200");
          messageBox.classList.remove("hidden");
        }
      });
  }

  const registerForm = document.querySelector("[data-register-form]");
  if (registerForm) {
    registerForm.addEventListener("submit", (event) => {
      const password = registerForm.elements.password.value;
      const confirmation = registerForm.elements.confirm_password.value;
      if (password !== confirmation) {
        event.preventDefault();
        if (messageBox) {
          messageBox.textContent = "The passwords do not match.";
          messageBox.classList.add("border-rose-900", "bg-rose-950/60", "text-rose-200");
          messageBox.classList.remove("hidden");
        }
      }
    });
  }

  const modal = document.querySelector("[data-users-modal]");
  if (!modal) return;

  const usersList = modal.querySelector("[data-users-list]");
  const userStatus = modal.querySelector("[data-user-status]");
  const createStatus = modal.querySelector("[data-create-status]");
  const addForm = modal.querySelector("[data-add-user-form]");
  const csrfToken = addForm.elements.csrf_token.value;
  const apiUrl = "../api/add-user.php";

  const setStatus = (element, text, isError = false) => {
    element.textContent = text;
    element.classList.toggle("text-rose-300", isError);
    element.classList.toggle("text-slate-400", !isError);
  };

  const loadUsers = async () => {
    setStatus(userStatus, "Loading accounts...");
    try {
      const response = await fetch(`${apiUrl}?action=list`, { credentials: "same-origin", headers: { Accept: "application/json" } });
      const data = await response.json();
      if (!response.ok) throw new Error(data.error || "Users could not be loaded.");
      usersList.replaceChildren();
      for (const user of data.users) {
        const row = document.createElement("tr");
        for (const value of [user.full_name, user.email, user.role]) {
          const cell = document.createElement("td");
          cell.className = "px-4 py-3 text-slate-300";
          cell.textContent = value;
          row.append(cell);
        }
        const actionCell = document.createElement("td");
        actionCell.className = "px-4 py-3";
        if (user.role !== "admin") {
          const removeButton = document.createElement("button");
          removeButton.className = "text-sm font-semibold text-rose-300 hover:text-rose-200";
          removeButton.type = "button";
          removeButton.textContent = "Remove";
          removeButton.addEventListener("click", async () => {
            if (!window.confirm(`Remove the account for ${user.full_name}?`)) return;
            try {
              const removeResponse = await fetch(apiUrl, {
                method: "POST",
                credentials: "same-origin",
                headers: { "Content-Type": "application/json", Accept: "application/json" },
                body: JSON.stringify({ action: "delete", id: user.id, csrf_token: csrfToken }),
              });
              const result = await removeResponse.json();
              if (!removeResponse.ok) throw new Error(result.error || "Account could not be removed.");
              await loadUsers();
            } catch (error) {
              setStatus(userStatus, error.message, true);
            }
          });
          actionCell.append(removeButton);
        } else {
          actionCell.textContent = "Protected";
          actionCell.className += " text-slate-500";
        }
        row.append(actionCell);
        usersList.append(row);
      }
      if (data.users.length === 0) {
        const row = document.createElement("tr");
        const cell = document.createElement("td");
        cell.colSpan = 4;
        cell.className = "px-4 py-4 text-slate-400";
        cell.textContent = "No accounts found.";
        row.append(cell);
        usersList.append(row);
      }
      setStatus(userStatus, `${data.users.length} account${data.users.length === 1 ? "" : "s"} found.`);
    } catch (error) {
      setStatus(userStatus, error.message, true);
    }
  };

  const openModal = () => {
    modal.classList.remove("hidden");
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("overflow-hidden");
    loadUsers();
  };
  const closeModal = () => {
    modal.classList.add("hidden");
    modal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("overflow-hidden");
  };

  document.querySelector("[data-open-users]").addEventListener("click", openModal);
  modal.querySelector("[data-close-users]").addEventListener("click", closeModal);
  modal.addEventListener("click", (event) => { if (event.target === modal) closeModal(); });
  document.addEventListener("keydown", (event) => { if (event.key === "Escape" && !modal.classList.contains("hidden")) closeModal(); });
  modal.querySelector("[data-refresh-users]").addEventListener("click", loadUsers);
  addForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    setStatus(createStatus, "Creating account...");
    const formData = new FormData(addForm);
    const payload = Object.fromEntries(formData.entries());
    try {
      const response = await fetch(apiUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: { "Content-Type": "application/json", Accept: "application/json" },
        body: JSON.stringify(payload),
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.error || "Account could not be created.");
      addForm.reset();
      addForm.elements.csrf_token.value = csrfToken;
      setStatus(createStatus, data.message);
      await loadUsers();
    } catch (error) {
      setStatus(createStatus, error.message, true);
    }
  });
})();
