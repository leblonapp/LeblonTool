<?php

namespace App\Models;

use Kreait\Firebase\Contract\Firestore;
use Google\Cloud\Core\Timestamp;
use DateTime;

abstract class FirestoreModel
{
    protected static string $collection = '';

    protected static function db()
    {
        $credentialsPath = '/Users/adrianfraga/LeblonApp/storage/app/firebase-cred.json';

        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath);

        $client = new \Google\Cloud\Firestore\FirestoreClient([
            'projectId' => 'leblon-app-build-h0vzr3',
        ]);

        return $client;
    }

    public static function all(): array
    {
        $docs = static::db()->collection(static::$collection)->documents();
        $items = [];
        foreach ($docs as $doc) {
            if ($doc->exists()) {
                $items[] = array_merge(['id' => $doc->id()], static::castData($doc->data()));
            }
        }
        return $items;
    }

    public static function find(string $id): ?array
    {
        $doc = static::db()->collection(static::$collection)->document($id)->snapshot();
        if (!$doc->exists())
            return null;
        return array_merge(['id' => $doc->id()], static::castData($doc->data()));
    }

    public static function create(array $data): string
    {
        $data['created_at'] = new Timestamp(new DateTime());
        $data['updated_at'] = new Timestamp(new DateTime());
        $ref = static::db()->collection(static::$collection)->add($data);
        return $ref->id();
    }

    public static function update(string $id, array $data): void
    {
        $data['updated_at'] = new Timestamp(new DateTime());
        static::db()->collection(static::$collection)->document($id)->set($data, ['merge' => true]);
    }

    public static function delete(string $id): void
    {
        static::db()->collection(static::$collection)->document($id)->delete();
    }

    protected static function castData(array $data): array
    {
        foreach ($data as $key => $value) {
            if ($value instanceof Timestamp) {
                $data[$key] = $value->get()->format('d/m/Y H:i');
            }
        }
        return $data;
    }

    //SUB-COLLECTIONS
    // ── Subcoleções ────────────────────────────────────────────

    public static function getSubCollection(string $docId, string $subCollection): array
    {
        $docs = static::db()
            ->collection(static::$collection)
            ->document($docId)
            ->collection($subCollection)
            ->documents();

        $items = [];
        foreach ($docs as $doc) {
            if ($doc->exists()) {
                $items[] = array_merge(['id' => $doc->id()], static::castData($doc->data()));
            }
        }
        return $items;
    }

    public static function createSubDocument(string $docId, string $subCollection, array $data): string
    {
        $data['created_at'] = new Timestamp(new DateTime());
        $data['updated_at'] = new Timestamp(new DateTime());

        $ref = static::db()
            ->collection(static::$collection)
            ->document($docId)
            ->collection($subCollection)
            ->add($data);

        return $ref->id();
    }

    public static function updateSubDocument(string $docId, string $subCollection, string $subDocId, array $data): void
    {
        $data['updated_at'] = new Timestamp(new DateTime());

        static::db()
            ->collection(static::$collection)
            ->document($docId)
            ->collection($subCollection)
            ->document($subDocId)
            ->set($data, ['merge' => true]);
    }

    public static function deleteSubDocument(string $docId, string $subCollection, string $subDocId): void
    {
        static::db()
            ->collection(static::$collection)
            ->document($docId)
            ->collection($subCollection)
            ->document($subDocId)
            ->delete();
    }
}
