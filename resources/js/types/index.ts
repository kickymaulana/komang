export interface User {
    id: number;
    name: string;
    email: string;
}

export interface Role {
    id: number;
    name: string;
    guard_name: string;
}

export interface PageProps {
    auth: {
        user: User | null;
        roles: string[];
    };
    flash?: {
        success?: string;
    };
    [key: string]: unknown;
}

export interface Pelanggan {
    id: number;
    idpel: string;
    nama: string;
    alamat: string;
    trf: string;
    daya: string;
    kddk: string;
}

export interface Paginated<T> {
    data: T[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    first_page_url: string;
    last_page_url: string;
    next_page_url: string | null;
    prev_page_url: string | null;
    path: string;
}

export interface UserAdmin extends User {
    roles: Role[];
    created_at: string;
}
