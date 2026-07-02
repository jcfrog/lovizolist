function app() {
    return {
        lists: [],
        items: [],
        currentList: null,
        newListName: '',
        newItemName: '',
        pollTimer: null,

        init() {
            this.loadLists();
            this.pollTimer = setInterval(() => {
                if (this.currentList) {
                    this.loadItems(this.currentList.id, true);
                } else {
                    this.loadLists(true);
                }
            }, window.APP_CONFIG.pollIntervalMs);
        },

        sortedItems() {
            return [...this.items].sort((a, b) => (a.is_checked - b.is_checked) || (a.id - b.id));
        },

        hasCheckedItems() {
            return this.items.some((item) => item.is_checked);
        },

        async loadLists(silent = false) {
            try {
                const res = await fetch('api/lists.php');
                if (!res.ok) throw new Error();
                this.lists = await res.json();
            } catch (e) {
                if (!silent) alert('Impossible de charger les listes.');
            }
        },

        async createList() {
            const name = this.newListName.trim();
            if (!name) return;
            this.newListName = '';
            try {
                const res = await fetch('api/lists.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name }),
                });
                if (!res.ok) throw new Error();
                await this.loadLists();
            } catch (e) {
                alert('Impossible de créer la liste.');
            }
        },

        async deleteList(list) {
            if (!confirm(`Supprimer la liste "${list.name}" ?`)) return;
            const backup = this.lists;
            this.lists = this.lists.filter((l) => l.id !== list.id);
            try {
                const res = await fetch(`api/lists.php?id=${list.id}`, { method: 'DELETE' });
                if (!res.ok) throw new Error();
            } catch (e) {
                this.lists = backup;
                alert('Impossible de supprimer la liste.');
            }
        },

        openList(list) {
            this.currentList = list;
            this.loadItems(list.id);
        },

        closeList() {
            this.currentList = null;
            this.items = [];
            this.loadLists();
        },

        async loadItems(listId, silent = false) {
            try {
                const res = await fetch(`api/items.php?list_id=${listId}`);
                if (!res.ok) throw new Error();
                this.items = await res.json();
            } catch (e) {
                if (!silent) alert('Impossible de charger les articles.');
            }
        },

        async addItem() {
            const name = this.newItemName.trim();
            if (!name || !this.currentList) return;
            this.newItemName = '';
            try {
                const res = await fetch('api/items.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ list_id: this.currentList.id, name }),
                });
                if (!res.ok) throw new Error();
                await this.loadItems(this.currentList.id);
                this.$refs.itemInput?.focus();
            } catch (e) {
                alert("Impossible d'ajouter l'article.");
            }
        },

        async toggleItem(item) {
            const previous = item.is_checked;
            item.is_checked = previous ? 0 : 1;
            try {
                const res = await fetch('api/items.php', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: item.id, is_checked: item.is_checked }),
                });
                if (!res.ok) throw new Error();
            } catch (e) {
                item.is_checked = previous;
                alert("Impossible de mettre à jour l'article.");
            }
        },

        async deleteItem(item) {
            const backup = this.items;
            this.items = this.items.filter((i) => i.id !== item.id);
            try {
                const res = await fetch(`api/items.php?id=${item.id}`, { method: 'DELETE' });
                if (!res.ok) throw new Error();
            } catch (e) {
                this.items = backup;
                alert("Impossible de supprimer l'article.");
            }
        },

        async clearChecked() {
            if (!this.currentList) return;
            const backup = this.items;
            this.items = this.items.filter((i) => !i.is_checked);
            try {
                const res = await fetch(`api/items.php?clear_checked_for_list=${this.currentList.id}`, { method: 'DELETE' });
                if (!res.ok) throw new Error();
            } catch (e) {
                this.items = backup;
                alert('Impossible de nettoyer les articles cochés.');
            }
        },
    };
}
