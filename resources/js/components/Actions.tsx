import type { GivingConfig } from '../types/content';
import { useContent } from '../content';
import { useState, type FormEvent } from 'react';
import { ArrowUpRight, LockKeyhole, Mail, ShieldCheck } from './icons';
import { safeExternalUrl } from '../content';
import { buildMailto } from '../lib/mailto';

import { CopyButton, TextLink } from './UI';
import { SelectField } from './SelectField';
export function GivingPanel({ config: suppliedConfig }: { config?: GivingConfig }) {
  const { site, ui } = useContent();
  const config = suppliedConfig ?? site.giving;
  const url = config.enabled ? safeExternalUrl(config.externalDonationUrl) : undefined;
  const bank = config.enabled && config.bankTransfer?.approved ? config.bankTransfer : null;
  return (
    <div className="giving-panel">
      <div className="giving-icon">
        <ShieldCheck size={35} weight="regular" aria-hidden="true" />
      </div>
      <p className="eyebrow">THOUGHTFUL GIVING</p>
      <h2>{ui.actions.givingTitle}</h2>
      {url ? (
        <>
          <p>
            You’ll continue to {config.providerName || 'our donation provider'} to make your
            contribution. Payments are handled by that provider.
          </p>
          <a href={url} className="button button-primary" target="_blank" rel="noopener noreferrer">
            Continue to secure donation provider
            <ArrowUpRight size={19} aria-hidden="true" />
          </a>
        </>
      ) : bank ? (
        <>
          <p>Support {site.brand.publicName} by bank transfer</p>
          <dl className="bank-details">
            <dt>Account name</dt>
            <dd>{bank.accountName}</dd>
            <dt>Bank</dt>
            <dd>{bank.bank}</dd>
            <dt>Account number</dt>
            <dd>{bank.accountNumber}</dd>
          </dl>
          <CopyButton value={bank.accountNumber} label="Copy account number" />
          <p>{bank.instructions}</p>
        </>
      ) : (
        <>
          <p>{config.unavailable}</p>
          <TextLink to="/contact">Contact information</TextLink>
        </>
      )}
      <div className="handoff-note">
        <LockKeyhole size={16} aria-hidden="true" />
        <span>{ui.actions.handoff}</span>
      </div>
    </div>
  );
}
export function EmailComposer({
  topic = 'General enquiry',
  destination: suppliedDestination,
}: {
  topic?: string;
  destination?: string;
}) {
  const { site, ui } = useContent();
  const destination = suppliedDestination ?? (site.contact.emailVerified ? site.contact.email : '');
  const [name, setName] = useState('');
  const [replyEmail, setReplyEmail] = useState('');
  const [subject, setSubject] = useState(topic);
  const [message, setMessage] = useState('');
  const [error, setError] = useState('');
  const available = Boolean(
    buildMailto(destination, '', { name: '', replyEmail: '', message: '' }),
  );
  function compose(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    if (!available) return;
    const url = buildMailto(destination, subject, {
      name: name.trim(),
      replyEmail: replyEmail.trim(),
      message: message.trim(),
    });
    if (!name.trim() || !message.trim() || !url) {
      setError('Please enter your name and message.');
      return;
    }
    setError('');
    window.location.href = url;
  }
  return (
    <div className="email-composer">
      <div className="composer-heading">
        <Mail size={28} weight="regular" aria-hidden="true" />
        <div>
          <h2>{ui.actions.emailTitle}</h2>
          <p>{ui.actions.emailText}</p>
        </div>
      </div>
      {!available && (
        <div className="availability-note" role="note">
          {site.contact.unavailable}
        </div>
      )}
      <form onSubmit={compose}>
        <fieldset disabled={!available}>
          <legend className="sr-only">Compose an enquiry</legend>
          <div className="form-two">
            <label>
              Your name
              <input
                required
                value={name}
                onChange={(e) => setName(e.target.value)}
                autoComplete="name"
                placeholder="Full name"
                maxLength={120}
              />
            </label>
            <label>
              Your email
              <input
                required
                type="email"
                value={replyEmail}
                onChange={(e) => setReplyEmail(e.target.value)}
                autoComplete="email"
                placeholder="you@example.com"
                maxLength={180}
              />
            </label>
          </div>
          <SelectField
            label="What would you like to discuss?"
            name="topic"
            value={subject}
            onValueChange={setSubject}
            disabled={!available}
            options={site.contact.topics.map((value) => ({ value, label: value }))}
          />
          <label>
            Your message
            <textarea
              required
              value={message}
              onChange={(e) => setMessage(e.target.value)}
              rows={5}
              maxLength={3000}
              placeholder="How can we help you take the next step?"
            />
          </label>
          {error && (
            <p role="alert" className="form-error">
              {error}
            </p>
          )}
          <button className="button button-primary" type="submit">
            Submit message
            <ArrowUpRight size={18} aria-hidden="true" />
          </button>
        </fieldset>
      </form>
    </div>
  );
}
