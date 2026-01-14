-- Add contact_name, contact_email, and contact_phone fields to addresses table
ALTER TABLE addresses 
ADD COLUMN IF NOT EXISTS contact_name VARCHAR(255) NULL AFTER user_id,
ADD COLUMN IF NOT EXISTS contact_email VARCHAR(255) NULL AFTER contact_name,
ADD COLUMN IF NOT EXISTS contact_phone VARCHAR(20) NULL AFTER contact_email;

-- For MySQL versions that don't support IF NOT EXISTS, use this instead:
-- ALTER TABLE addresses ADD COLUMN contact_name VARCHAR(255) NULL AFTER user_id;
-- ALTER TABLE addresses ADD COLUMN contact_email VARCHAR(255) NULL AFTER contact_name;
-- ALTER TABLE addresses ADD COLUMN contact_phone VARCHAR(20) NULL AFTER contact_email;

